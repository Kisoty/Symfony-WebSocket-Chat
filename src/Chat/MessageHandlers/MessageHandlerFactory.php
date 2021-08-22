<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Psr\Container\ContainerInterface;

class MessageHandlerFactory
{
    public function __construct(private ContainerInterface $container) {}

    /**
     * @param string $method
     * @return object
     * @throws HandlerNotFoundException
     */
    public function getHandler(string $method): object
    {
        return match ($method) {
            'message' => $this->container->get(SendMessageHandler::class),
            'changeName' => $this->container->get(ChangeNameHandler::class),
            default => throw new HandlerNotFoundException('No handler for method "' . $method .'"')
        };
    }
}
