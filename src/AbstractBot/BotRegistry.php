<?php

namespace Longman\TelegramBot\AbstractBot;

use LogicException;
use Longman\TelegramBot\AbstractBot\Entity\Chat;
use Longman\TelegramBot\TelegramApi;
use Psr\Log\LoggerInterface;
use RuntimeException;

class BotRegistry
{
    public function __construct(private TelegramApi $telegramBot, private LoggerInterface $logger)   //todo #refactor configuration
    {
    }

    public function getBot(Chat $chat): BotInterface
    {
        if ('telegram' !== $chat->getBotId()) {
            throw new LogicException('Unknown bot for chat.');
        }

        return $this->telegramBot;
    }

    public function getBotByRequestData(array $requestData): ?BotInterface
    {
        if (!array_key_exists('message', $requestData) ) {
            return null;
        }

        return $this->telegramBot;
    }
}
