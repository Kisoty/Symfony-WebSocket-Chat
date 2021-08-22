<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\Receivers;

use Kisoty\WebSocketChat\Chat\Chat;

class AllChatUsers implements ReceiverInterface
{
    public function __construct(private Chat $chat) {}

    public function receiveMessage(string $message): void
    {
        $this->chat->sendToAll($message);
    }
}
