<?php

declare(strict_types=1);

namespace ConsoleGpt\Command;

use ConsoleGpt\Llm\ChatFactory;
use ConsoleGpt\Llm\ChatGpt\ChatGpt;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ChatCommand extends Command
{
    public const DEFAULT_COMMAND_NAME = 'chat';
    public const DEFAULT_LLM = ChatGpt::class;

    public function __construct(
        private readonly string $llmChatClassName = self::DEFAULT_LLM,
        string $name = self::DEFAULT_COMMAND_NAME
    ) {
        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('A command that allows you to interact with the symfony console using regular chat');
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ): int {
        $application = $this->getApplication();
        if (!$application) {
            return self::FAILURE;
        }
        $style = new SymfonyStyle($input, $output);
        $llmChat = (new ChatFactory($style, $application))->createLlmChat($this->llmChatClassName);

        $style->text(<<<GREETING
Hello! My name is ConsoleGpt, and I am your assistant for working with the console application. 
Write what command or action you want to perform, and I will help you do it!
GREETING);
        $llmChat->run();

        return Command::SUCCESS;
    }
}
