<?php

namespace Longman\TelegramBot\AbstractBot\Entity;

class Chat
{
    public function __construct(private int|string $chatId) {}

    public function getChatId(): string|int
    {
        return $this->chatId;
    }

    public function getBotId(): string|int
    {
        return 'telegram';
    }
}
