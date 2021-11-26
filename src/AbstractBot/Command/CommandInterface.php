<?php

namespace Longman\TelegramBot\AbstractBot\Command;

use Longman\TelegramBot\AbstractBot\BotInterface;
use Longman\TelegramBot\AbstractBot\Entity\CommandData;
use Longman\TelegramBot\AbstractBot\Exception\HandleCommandException;

interface CommandInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function getHelp(): string;

    /**
     * @throws HandleCommandException
     */
    public function execute(BotInterface $bot, CommandData $commandData): void;
}
