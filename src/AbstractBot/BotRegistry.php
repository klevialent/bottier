<?php

namespace Longman\TelegramBot\AbstractBot;

use LogicException;
use Longman\TelegramBot\AbstractBot\Entity\Chat;
use Longman\TelegramBot\TelegramApi;
use RuntimeException;

class BotRegistry
{
    public function __construct(private TelegramApi $telegramBot)   //todo #refactor configuration
    {
    }

    public function getBot(Chat $chat): BotInterface
    {
        if ('telegram' !== $chat->getBotId()) {
            throw new LogicException('Unknown bot for chat.');
        }

        return $this->telegramBot;
    }

    public function getBotByRequestData(array $requestData): BotInterface
    {
        if (!array_key_exists('message', $requestData) ) {
            throw new RuntimeException('Unknown bot for request.');
        }

        return $this->telegramBot;
    }
}
