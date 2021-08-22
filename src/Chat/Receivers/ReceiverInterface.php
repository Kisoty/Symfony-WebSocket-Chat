<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\Receivers;

interface ReceiverInterface
{
    public function receiveMessage(string $message): void;
}
