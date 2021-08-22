<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat;

use Kisoty\WebSocketChat\Chat\Receivers\ReceiverInterface;

class ChatUser implements ReceiverInterface
{
    public function __construct(private Chat $chat, private int $id, private string $name) {}

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $newName): void
    {
        $this->name = $newName;
    }

    public function receiveMessage(string $message): void
    {
        $this->chat->sendToUser($message, $this);
    }
}
