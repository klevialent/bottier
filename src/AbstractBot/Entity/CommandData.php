<?php

namespace Longman\TelegramBot\AbstractBot\Entity;

interface CommandData
{
    public function getName(): string;

    public function getArguments(): array;

    public function getArgument(int|string $key): string;

    public function getChat(): Chat;

    public function getChatId(): string|int;

    public function getUsername(): string;
}
