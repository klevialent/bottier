<?php

declare(strict_types=1);

namespace Longman\TelegramBot;

use Longman\TelegramBot\Entities\ServerResponse;
use Longman\TelegramBot\Exception\InvalidBotTokenException;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TelegramApi
{
    public function __construct(
        private Telegram $telegram,
        private HttpClientInterface $tgHttpClient,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws InvalidBotTokenException
     * @throws TelegramException
     */
    public function sendMessage(string $message, int|string $chatId): ServerResponse
    {
        $data = [
            'text' => $message,
            'chat_id' => $chatId,
        ];

        return $this->act('sendMessage', $data);
    }

    /**
     * @throws InvalidBotTokenException
     * @throws TelegramException
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
}
