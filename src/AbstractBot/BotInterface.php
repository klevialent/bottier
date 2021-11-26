<?php

namespace Longman\TelegramBot\AbstractBot;

use Longman\TelegramBot\AbstractBot\Entity\CommandData;
use Longman\TelegramBot\AbstractBot\Exception\SendMessageException;

interface BotInterface
{
    /**
     * @throws SendMessageException
     */
    public function sendMessage(string $message, int|string $chatId): void;

    public function getEntityCommandData(array $commandData): CommandData;
}
