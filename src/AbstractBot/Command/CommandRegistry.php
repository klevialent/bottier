<?php

namespace Longman\TelegramBot\AbstractBot\Command;

use Longman\TelegramBot\AbstractBot\Entity\CommandData;
use Longman\TelegramBot\AbstractBot\Exception\CommandNotFoundException;
use RuntimeException;

class CommandRegistry
{
    /** @param iterable|CommandInterface[] $commands */
    public function __construct(private iterable $commands) {}

    /**
     * @throws CommandNotFoundException
     */
    public function getCommand(CommandData $commandData): CommandInterface
    {
        foreach ($this->commands as $command) {
            if ($command->getName() === $commandData->getName()) {
                return $command;
            }
        }

        throw new CommandNotFoundException($commandData->getName());
    }
}
