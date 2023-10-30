<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;

final class ConsoleCommandFunction extends BaseFunction
{
    private const MAX_OUTPUT_LEN_TO_PROCESS = 1500;
    private const ARGUMENTS_TO_SKIP = [
        'command',
        '--quiet',
        '--no-ansi',
        '--ansi',
        '--version'
    ];

    public function __construct(
        private readonly SymfonyStyle $io,
        private readonly string $name,
        private readonly string $description,
        private readonly array $arguments,
    ) {
    }

    public static function createByConsoleCommand(
        SymfonyStyle $io,
        Command $command
    ): ConsoleCommandFunction {
        $arguments = [];
        foreach ($command->getDefinition()->getArguments() as $argument) {
            $functionName = $argument->getName();
            if (in_array($functionName, self::ARGUMENTS_TO_SKIP)) {
                continue;
            }
            $arguments[$functionName] = new FunctionArgument(
                $functionName,
                ['string'],
                $argument->getDescription(),
                $argument->isRequired()
            );
        }
        foreach ($command->getDefinition()->getOptions() as $option) {
            $functionName = "--{$option->getName()}";
            if (in_array($functionName, self::ARGUMENTS_TO_SKIP)) {
                continue;
            }

            if (!$option->isValueRequired()) {
                $valueType = ["null"];
            } elseif ($option->isValueOptional()) {
                $valueType = ["string", "null"];
            } else {
                $valueType = ["string"];
            }

            $arguments[$functionName] = new FunctionArgument(
                $functionName,
                $valueType,
                $option->getDescription(),
                false
            );
        }
        return new self(
            $io,
            $command->getName(),
            $command->getDescription() . ($command->getSynopsis() ? '. Synopsis: ' . $command->getSynopsis() : ''),
            $arguments
        );
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function run(array $values = []): string
    {
        $declaredArgs = $this->getArguments();
        array_filter($values, static fn(string $k) => array_key_exists($k, $declaredArgs), ARRAY_FILTER_USE_KEY);

        $arguments = array_filter($values, static fn(string $key) => !str_starts_with($key, '-'), ARRAY_FILTER_USE_KEY);
        $arguments = array_map(static fn($v) => $v, $arguments);

        $options = array_filter($values, static fn(string $key) => str_starts_with($key, '-'), ARRAY_FILTER_USE_KEY);
        $options = array_map(static function (string $k, ?string $v) use ($declaredArgs): string {
            if ($v === 'null' || is_null($v)) {
                return $k;
            }
            $function = $declaredArgs[$k] ?? null;
            if ($function?->getTypes() === ['null']) {
                return $k;
            }
            return "{$k}={$v}";
        }, array_keys($options), $options);

        $process = new Process(
            command: [
                PHP_BINARY,
                $_SERVER['PHP_SELF'],
                $this->getName(),
                ...$arguments,
                ...$options,
            ],
            input: STDIN,
            timeout: 3600
        );
        $this->io->text("Run command: {$process->getCommandLine()}");
        $process->setPty(true);
        $process->start();
        $iterator = $process->getIterator($process::ITER_SKIP_ERR | $process::ITER_KEEP_OUTPUT);
        $this->io->section('========');
        $this->io->write($iterator);
        $this->io->section('========');

        $output = mb_substr($process->getOutput(), self::MAX_OUTPUT_LEN_TO_PROCESS * -1);
        return $output ?: '[empty_result]';
    }
}
