<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\Receivers;

use Kisoty\WebSocketChat\Chat\MessageDispatcher;
use Kisoty\WebSocketChat\Chat\ChatUser;

class ChatUserBatch implements ReceiverInterface
{
    public function __construct(
        private MessageDispatcher $dispatcher,
        /**
         * @var ChatUser[] $users
         */
        private array $users = []
    ) {}

    public function receiveMessage(string $message): void
    {
        foreach ($this->users as $user) {
            $this->dispatcher->sendToUser($message, $user);
        }
    }
}
