<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

final class FunctionArgument
{
    public function __construct(
        private readonly string $name,
        private readonly array $types,
        private readonly string $description,
        private readonly bool $isRequired
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTypes(): array
    {
        return $this->types;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function isRequired(): bool
    {
        return $this->isRequired;
    }
}
