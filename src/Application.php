<?php

declare(strict_types=1);

namespace ConsoleGpt;

use ConsoleGpt\Command\ChatCommand;

class Application extends \Symfony\Component\Console\Application
{
    protected string $chatCommandName = ChatCommand::DEFAULT_COMMAND_NAME;
    protected string $LlmChatClassName = ChatCommand::DEFAULT_LLM;

    protected function getDefaultCommands(): array
    {
        $defaultCommands =  parent::getDefaultCommands();
        $defaultCommands[] = new ChatCommand($this->LlmChatClassName, $this->chatCommandName);
        return $defaultCommands;
    }
}
