<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;

final class FunctionsCollection implements \IteratorAggregate
{
    /**
     * @var FunctionInterface[]
     */
    private array $functions = [];

    public function __construct(private readonly SymfonyStyle $io)
    {
    }

    public function getIterator(): \Generator
    {
        yield from $this->functions;
    }

    public function loadFromConsoleApp(Application $app): FunctionsCollection
    {
        foreach ($app->all() as $command) {
            if ($command->isHidden() || !$command->isEnabled()) {
                continue;
            }
            $this->add(ConsoleCommandFunction::createByConsoleCommand($this->io, $command));
        }
        return $this;
    }

    public function add(FunctionInterface $function): FunctionsCollection
    {
        $this->functions[$function->getName()] = $function;
        return $this;
    }

    public function get(string $functionName): ?FunctionInterface
    {
        return $this->functions[$functionName] ?? null;
    }

    public function asArray(): array
    {
        $functions = [];
        foreach ($this->functions as $function) {
            $functions[] = $function->asArray();
        }
        return $functions;
    }
}
