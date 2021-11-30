<?php

namespace Longman\TelegramBot\AbstractBot\Exception;

class CommandNotFoundException extends HandleCommandException
{
    public function __construct(string $command)
    {
        parent::__construct("Command '$command' not found.", 0, null);
    }
}
