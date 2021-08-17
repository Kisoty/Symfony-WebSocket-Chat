<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Psr\Container\ContainerInterface;

class MessageHandlerFactory
{
    public function __construct(private ContainerInterface $container) {}

    public function getHandler(string $method): MessageHandlerInterface
    {
        return match ($method) {
            'message' => $this->container->get(SendMessageHandler::class),
            'changeName' => $this->container->get(ChangeNameHandler::class)
        };
    }
}
