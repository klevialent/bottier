<?php

namespace Longman\TelegramBot\Entity;

use Longman\TelegramBot\AbstractBot\Entity\AbstractEntity;
use Longman\TelegramBot\AbstractBot\Entity\Chat;
use Longman\TelegramBot\AbstractBot\Entity\CommandData;

class TelegramCommandData extends AbstractEntity implements CommandData
{
    private array $message;

    private string $name;

    /**
     * @var string[]
     */
    private array $arguments;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->message = $this->data['message'];

        $explodedText = explode(' ', $this->getText());

        $this->name = ltrim(array_shift($explodedText), '/');
        $this->arguments = $explodedText;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function getArgument(int|string $key): ?string
    {
        if (array_key_exists($key, $this->arguments)) {
            return (string)$this->arguments[$key];
        }
        
        return null;
    }

    public function getUsername(): string
    {
        return $this->message['from']['username'];
    }

    private function getText(): string
    {
        return $this->message['text'];
    }

    public function getChat(): Chat
    {
        return new Chat($this->getChatId());
    }

    public function getChatId(): string|int
    {
        return $this->message['chat']['id'];
    }
}
