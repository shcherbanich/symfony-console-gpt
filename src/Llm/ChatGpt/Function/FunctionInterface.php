<?php

namespace ConsoleGpt\Llm\ChatGpt\Function;

interface FunctionInterface
{
    public function getName(): string;

    public function getDescription(): string;

    /**
     * @return FunctionArgument[]
     */
    public function getArguments(): array;

    public function run(array $values = []): string;

    public function asArray(): array;
}
