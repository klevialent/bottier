<?php

namespace Longman\TelegramBot\AbstractBot\Entity;

abstract class AbstractEntity
{
    public function __construct(protected array $data) {}
}
