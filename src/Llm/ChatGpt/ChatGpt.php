<?php

declare(strict_types=1);

namespace ConsoleGpt\Llm\ChatGpt;

use ConsoleGpt\Llm\Chat;
use ConsoleGpt\Llm\ChatGpt\Function\ChangeDialogMessagesLimitFunction;
use ConsoleGpt\Llm\ChatGpt\Function\ChangeGptModelFunction;
use ConsoleGpt\Llm\ChatGpt\Function\ExitChatFunction;
use ConsoleGpt\Llm\ChatGpt\Function\FunctionsCollection;
use ConsoleGpt\Llm\ChatGpt\Function\ShowCurrentGptModelFunction;
use ConsoleGpt\Llm\ChatGpt\Function\ShowDialogMessagesLimitFunction;
use ConsoleGpt\Llm\ChatGpt\Function\ShowUsageStatisticFunction;
use Gioni06\Gpt3Tokenizer\Gpt3Tokenizer;
use Gioni06\Gpt3Tokenizer\Gpt3TokenizerConfig;
use JetBrains\PhpStorm\NoReturn;
use JsonException;
use OpenAI;
use OpenAI\Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Style\SymfonyStyle;

final class ChatGpt implements Chat
{
    private const PROMPTS_DIR_NAME = __DIR__ . DIRECTORY_SEPARATOR . 'prompts';

    private array $dialogMessages = [];
    private int $dialogMessagesLimit = 10;
    private Gpt3Tokenizer $tokenizer;
    private string $model = 'gpt-4';
    private array $usageStatistic = [];

    private function __construct(
        private readonly SymfonyStyle $io,
        private readonly Client $client,
        private readonly Application $app
    ) {
        $config = new Gpt3TokenizerConfig();
        $this->tokenizer = new Gpt3Tokenizer($config);
    }

    public static function create(SymfonyStyle $io, Application $app): ChatGpt
    {
        $openaiKey = getenv('OPENAI_API_KEY');
        $client = OpenAI::factory()
            ->withApiKey($openaiKey)
            ->withHttpClient($client = new \GuzzleHttp\Client([]))
            ->withStreamHandler(fn(RequestInterface $request): ResponseInterface => $client->send($request, [
                'stream' => true
            ]))
            ->make();
        $chatGpt = new self($io, $client, $app);
        $chatGpt->choiceModel();
        return $chatGpt;
    }

    /**
     * @throws JsonException
     */
    #[NoReturn] public function run(): void
    {
        $functionsCollection = $this->getFunctionsCollection();

        do {
            $question = $this->io->ask('You') ?: '';
            $question = mb_convert_encoding($question, 'UTF-8', 'UTF-8');
        } while (!$question);

        $this->addMessageToDialog('user', $question);

        while (true) {
            $assistantResponse = '';
            $functionCall = [
                'name' => '',
                'arguments' => '',
            ];
            $sendStreamedRequest = function () use ($functionsCollection, &$assistantResponse, &$functionCall): \Generator {
                $stream = $this->client->chat()->createStreamed([
                    'model' => $this->getModel(),
                    'messages' => $this->getDialogHistoryForRequest(),
                    'functions' => $functionsCollection->asArray(),
                ]);
                $outputTokens = 0;
                $lastModel = '';
                foreach ($stream as $k => $response) {
                    ++$outputTokens;
                    $lastModel = $response['model'] ?? $this->getModel();
                    if ($response->choices[0]->delta->functionCall) {
                        $fc = $response->choices[0]->delta->functionCall->toArray();
                        $functionCall['name'] .= $fc['name'] ?? '';
                        $functionCall['arguments'] .= $fc['arguments'] ?? '';
                        continue;
                    }
                    if (!$k) {
                        yield "<info>ConsoleGpt:</info> ";
                    }
                    yield $response->choices[0]->delta->content ?: '';

                    $assistantResponse .= $response->choices[0]->delta->content ?: '';
                }
                $this->addModelUsageStatistic($lastModel, $this->calculateDialogHistoryTokens(), $outputTokens);
            };

            $this->io->write($sendStreamedRequest());
            $this->addMessageToDialog('assistant', $assistantResponse);

            if (!$functionCall['name']) {
                break;
            }

            $args = json_decode($functionCall['arguments'], true, 512, JSON_THROW_ON_ERROR);
            $output = $functionsCollection->get($functionCall['name'])?->run($args) ?: '[function_not_found]';
            $this->addFunctionResultToDialog($functionCall['name'], $output);
        }

        $this->run();
    }

    public function addMessageToDialog(string $role, string $message): void
    {
        if ($message) {
            $this->dialogMessages[] = ['role' => $role, 'content' => $message];
        }
    }

    public function addFunctionResultToDialog(string $functionName, string $result): void
    {
        $this->dialogMessages[] = ['role' => 'function', 'name' => $functionName, 'content' => $result];
    }

    public function choiceModel(): void
    {
        $models = array_column($this->client->models()->list()->toArray()['data'] ?? [], 'id');
        $models = array_filter($models, static fn($id) => str_starts_with($id, 'gpt-') && preg_match('/^(?!.*\b(audio|realtime)\b)/', $id));
        rsort($models);
        $this->model = $this->io->choice('Please select a model from the list of available ones', $models, 'gpt-4');
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function calculateDialogHistoryTokens(): int
    {
        $numberOfTokens = 0;
        foreach ($this->getDialogHistoryForRequest() as $message) {
            $numberOfTokens += $this->tokenizer->count($message['content']);
        }
        return $numberOfTokens;
    }

    public function getUsageStatistic(): array
    {
        return $this->usageStatistic;
    }

    public function getDialogMessagesLimit(): int
    {
        return $this->dialogMessagesLimit;
    }
    public function setDialogMessagesLimit(int $limit): int
    {
        return $this->dialogMessagesLimit = $limit;
    }

    public function getCurrentDialogMessagesCount(): int
    {
        return count($this->getDialogHistoryForRequest());
    }

    private function getDialogHistoryForRequest(): array
    {
        $dialog[] = [
            'role' => 'system',
            'content' => file_get_contents(self::PROMPTS_DIR_NAME . '/01_system_message.txt')
        ];

        return array_merge(
            $dialog,
            array_slice($this->dialogMessages, $this->dialogMessagesLimit * -1, $this->dialogMessagesLimit)
        );
    }

    private function getFunctionsCollection(): FunctionsCollection
    {
        $collection = new FunctionsCollection($this->io);
        $collection->loadFromConsoleApp($this->app);
        $collection->add(new ExitChatFunction());
        $collection->add(new ChangeGptModelFunction($this));
        $collection->add(new ShowCurrentGptModelFunction($this));
        $collection->add(new ShowUsageStatisticFunction($this));
        $collection->add(new ShowDialogMessagesLimitFunction($this));
        $collection->add(new ChangeDialogMessagesLimitFunction($this));
        return $collection;
    }

    private function addModelUsageStatistic(string $model, int $inputTokens, int $outputTokens): void
    {
        $this->usageStatistic[$model] ??= [
            'inputTokens' => 0,
            'outputTokens' => 0,
        ];
        $this->usageStatistic[$model]['inputTokens'] += $inputTokens;
        $this->usageStatistic[$model]['outputTokens'] += $outputTokens;
    }
}
