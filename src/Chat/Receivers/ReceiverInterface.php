<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\Receivers;

use Kisoty\WebSocketChat\Chat\Chat;

interface ReceiverInterface
{
    public function receiveMessage(Chat $chat, string $message): void;
}
