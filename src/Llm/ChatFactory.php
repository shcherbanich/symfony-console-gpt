<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ChatFactory
{
    public function __construct(private readonly SymfonyStyle $io, private readonly Application $app)
    {
    }

    public function createLlmChat(string $llmClassName): Chat
    {
        if (!class_exists($llmClassName) || !is_a($llmClassName, Chat::class, true)) {
            throw new \InvalidArgumentException();
        }

        return $llmClassName::create($this->io, $this->app);
    }
}
