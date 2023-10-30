<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

use ConsoleGpt\Llm\ChatGpt\ChatGpt;

final class ChangeGptModelFunction extends BaseFunction
{
    public function __construct(private readonly ChatGpt $chatGpt)
    {
    }

    public function getName(): string
    {
        return '__change_model';
    }

    public function getDescription(): string
    {
        return 'Change GPT model';
    }

    public function getArguments(): array
    {
        return [];
    }

    public function run(array $values = []): string
    {
        $this->chatGpt->choiceModel();
        return 'Change GPT model changed';
    }
}
