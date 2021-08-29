<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\Receivers;

use Kisoty\WebSocketChat\Chat\MessageDispatcher;

class AllChatUsers implements ReceiverInterface
{
    public function __construct(private MessageDispatcher $dispatcher) {}

    public function receiveMessage(string $message): void
    {
        $this->dispatcher->sendToAll($message);
    }
}
