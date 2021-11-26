<?php

namespace Longman\TelegramBot;

use JsonException;
use Longman\TelegramBot\AbstractBot\BotInterface;
use Longman\TelegramBot\AbstractBot\Entity\CommandData;
use Longman\TelegramBot\AbstractBot\Exception\SendMessageException;
use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Entity\TelegramCommandData;
use Longman\TelegramBot\Exception\InvalidBotTokenException;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramApi implements BotInterface
{
    public function __construct(
        private Telegram $telegram,
        private HttpClientInterface $tgHttpClient,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws SendMessageException
     */
    public function sendMessage(string $message, int|string $chatId): void
    {
        $data = [
            'text' => $message,
            'chat_id' => $chatId,
        ];

        try {
            $this->act('sendMessage', $data);
        } catch (InvalidBotTokenException $tokenException) {
            throw new RuntimeException('Wrong telegram token.');
        } catch (TelegramException|JsonException $e) {
            throw new SendMessageException($e->getMessage(), 0, $e);
        }
    }

    /**
     * @throws InvalidBotTokenException
     * @throws TelegramException
     * @throws JsonException
     */
    private function act(string $action, array $data = []): ServerResponse
    {
        $botUsername = $this->telegram->getBotUsername();

        Request::limitTelegramRequests($action, $data);

        // Remember which action is currently being executed.
        Request::$current_action = $action;

        $rawResponse = $this->execute($action, $data);
        $response = json_decode($rawResponse, true, 512, JSON_THROW_ON_ERROR);

        if (null === $response) {
            $this->logger->debug($rawResponse);
            throw new TelegramException('Telegram returned an invalid response!');
        }

        $response = new ServerResponse($response, $botUsername);
        if (!$response->isOk() && $response->getErrorCode() === 401 && $response->getDescription() === 'Unauthorized') {
            throw new InvalidBotTokenException();
        }

        // Reset current action after completion.
        Request::$current_action = '';

        return $response;
    }

    /**
     * @throws TelegramException
     */
    private function execute(string $action, array $data = []): string
    {
        try {
            $response = $this->tgHttpClient->request('POST',
                '/bot' . $this->telegram->getApiKey() . '/' . $action,
                ['body' => $data],
            );
            $result = $response->getContent();
        } catch (ExceptionInterface $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            throw new TelegramException('Execute failed.', 0, $e);
        }

        return $result;
    }

    public function getEntityCommandData(array $commandData): CommandData
    {
        return new TelegramCommandData($commandData);
    }
}
