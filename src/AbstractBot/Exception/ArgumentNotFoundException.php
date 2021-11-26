<?php

namespace Longman\TelegramBot\AbstractBot\Exception;

class ArgumentNotFoundException extends EntityException
{
    public function __construct(protected string $key, protected array $arguments)
    {
        parent::__construct("Argument '$key' not found.");
    }
}
