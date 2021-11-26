<?php

namespace Longman\TelegramBot\AbstractBot\Command;

use Longman\TelegramBot\AbstractBot\Entity\CommandData;
use RuntimeException;

class CommandRegistry
{
    /** @param iterable|CommandInterface[] $commands */
    public function __construct(private iterable $commands) {}

    public function getCommand(CommandData $commandData): CommandInterface
    {
        foreach ($this->commands as $command) {
            if ($command->getName() === $commandData->getName()) {
                return $command;
            }
        }

        throw new RuntimeException("Command {$commandData->getName()} not found.");
    }
}
