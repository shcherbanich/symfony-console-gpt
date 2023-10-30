<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

use ConsoleGpt\Llm\ChatGpt\ChatGpt;

final class ShowCurrentGptModelFunction extends BaseFunction
{
    public function __construct(private readonly ChatGpt $chatGpt)
    {
    }

    public function getName(): string
    {
        return '__show_model';
    }

    public function getDescription(): string
    {
        return 'Show current GPT model';
    }

    public function getArguments(): array
    {
        return [];
    }

    public function run(array $values = []): string
    {
        return $this->chatGpt->getModel();
    }
}
