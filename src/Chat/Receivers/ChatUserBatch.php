<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\Receivers;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\ChatUser;

class ChatUserBatch implements ReceiverInterface
{
    public function __construct(
        /**
         * @var ChatUser[] $users
         */
        private array $users = []
    ) {}

    public function receiveMessage(Chat $chat, string $message): void
    {
        foreach ($this->users as $user) {
            $chat->sendToUser($message, $user);
        }
    }
}
