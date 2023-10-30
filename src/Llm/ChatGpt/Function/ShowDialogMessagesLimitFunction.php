<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

use ConsoleGpt\Llm\ChatGpt\ChatGpt;

final class ShowDialogMessagesLimitFunction extends BaseFunction
{
    public function __construct(private readonly ChatGpt $chatGpt)
    {
    }

    public function getName(): string
    {
        return '__show_messages_limit';
    }

    public function getDescription(): string
    {
        return 'Get the limit of messages stored in a conversation';
    }

    public function getArguments(): array
    {
        return [];
    }

    public function run(array $values = []): string
    {
        return (string)$this->chatGpt->getDialogMessagesLimit();
    }
}
