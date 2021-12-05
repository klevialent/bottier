<?php

namespace Longman\TelegramBot\AbstractBot;

use Longman\TelegramBot\AbstractBot\Command\CommandRegistry;
use Longman\TelegramBot\AbstractBot\Entity\Chat;
use Longman\TelegramBot\AbstractBot\Exception\CommandNotFoundException;
use Longman\TelegramBot\AbstractBot\Exception\HandleCommandException;
use Longman\TelegramBot\AbstractBot\Exception\SendMessageException;

class BotsApi
{
    public function __construct(private BotRegistry $botRegistry, private CommandRegistry $commandRegistry) {}

    /**
     * @throws HandleCommandException
     */
    public function handleCommand(array $commandData): void
    {
        $bot = $this->botRegistry->getBotByRequestData($commandData);
        if (!$bot) {
            return;
        }
        
        $commandEntity = $bot->getEntityCommandData($commandData);

        try {
            $command = $this->commandRegistry->getCommand($commandEntity);
        } catch (CommandNotFoundException $e) {
            $bot->sendMessage($e->getMessage(), $commandEntity->getChatId());
            return;
        }

        $command->execute($bot, $commandEntity);
    }

    /**
     * @throws SendMessageException
     */
    public function sendMessage(string $message, Chat $chat): void
    {
        $this->getBot($chat)->sendMessage($message, $chat->getChatId());
    }

    private function getBot(Chat $chat): BotInterface
    {
        return $this->botRegistry->getBot($chat);
    }
}
