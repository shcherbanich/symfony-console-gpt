<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

use JetBrains\PhpStorm\NoReturn;

final class ExitChatFunction extends BaseFunction
{
    public function getName(): string
    {
        return '__exit';
    }

    public function getDescription(): string
    {
        return 'Exit from chat';
    }

    public function getArguments(): array
    {
        return [];
    }

    #[NoReturn] public function run(array $values = []): string
    {
        exit();
    }
}
