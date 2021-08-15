<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

class MessageHandlerFactory
{
    public function getHandler(string $method): MessageHandlerInterface
    {
        return match ($method) {
            'message' => new SendMessageHandler(),
            'changeName' => new ChangeNameHandler()
        };
    }
}
