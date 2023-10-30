<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

use ConsoleGpt\Llm\ChatGpt\ChatGpt;

final class ChangeDialogMessagesLimitFunction extends BaseFunction
{
    public function __construct(private readonly ChatGpt $chatGpt)
    {
    }

    public function getName(): string
    {
        return '__change_messages_limit';
    }

    public function getDescription(): string
    {
        return 'Set a new message limit in a conversation';
    }

    public function getArguments(): array
    {
        return [
            new FunctionArgument('limit', ['integer'], 'New message limit', true)
        ];
    }

    public function run(array $values = []): string
    {
        $newLimit = $values['limit'] ?? 10;
        $this->chatGpt->setDialogMessagesLimit($newLimit);
        return "New message limit has been set: {$newLimit}";
    }
}
