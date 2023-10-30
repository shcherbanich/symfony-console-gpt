<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt\Function;

use ConsoleGpt\Llm\ChatGpt\ChatGpt;

final class ShowUsageStatisticFunction extends BaseFunction
{
    public function __construct(private readonly ChatGpt $chatGpt)
    {
    }

    public function getName(): string
    {
        return '__usage_statistic';
    }

    public function getDescription(): string
    {
        return 'Show usage statistics for this dialog';
    }

    public function getArguments(): array
    {
        return [];
    }

    /**
     * @throws \JsonException
     */
    public function run(array $values = []): string
    {
        return json_encode([
            'tokens_in_memory' => $this->chatGpt->calculateDialogHistoryTokens(),
            'messages_in_memory' => $this->chatGpt->getCurrentDialogMessagesCount() - 1,
            'history' => $this->chatGpt->getUsageStatistic()
        ], JSON_THROW_ON_ERROR);
    }
}
