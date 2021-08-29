<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\RequestFoundation;

use Kisoty\WebSocketChat\Chat\MessageDispatcher;
use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\MessageHandlers\HandlerNotFoundException;
use Kisoty\WebSocketChat\Chat\MessageHandlers\MessageHandlerFactory;
use Kisoty\WebSocketChat\Chat\RequestFoundation\MessageParser\MessageParser;
use Kisoty\WebSocketChat\Chat\RequestFoundation\MessageParser\WrongMessageFormatException;
use Kisoty\WebSocketChat\Chat\Receivers\ReceiverInterface;
use Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\ArgumentResolver;
use Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\DTOResolver;
use Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\Exception\ArgumentResolverException;

class RequestProcessor
{
    public function __construct(
        private MessageParser $messageParser,
        private MessageHandlerFactory $factory,
        private ArgumentResolver $argumentResolver,
        private DTOResolver $dtoResolver
    ) {}


    public function process(MessageDispatcher $dispatcher, ChatUser $sender, string $data): void
    {
        try {
            $this->messageParser->setMessage($data);
            $method = $this->messageParser->getMethod();
            $messageData = $this->messageParser->getMessageData();
            $receivers = $this->messageParser->getReceiversFromChat($dispatcher);
        } catch (WrongMessageFormatException $e) {
            $dispatcher->sendToUser($e->getMessage(), $sender);
            return;
        }

        try {
            $handler = $this->factory->getHandler($method);
        } catch (HandlerNotFoundException $e) {
            $dispatcher->sendToUser($e->getMessage(), $sender);
            return;
        }

        $this->argumentResolver->predefine(ChatUser::class, $sender);
        $this->argumentResolver->predefine(ReceiverInterface::class, $receivers);
        $this->argumentResolver->predefine(MessageDispatcher::class, $dispatcher);
        $this->argumentResolver->addResolver($this->dtoResolver);

        if (method_exists($handler, 'handle')) {
            try {
                $arguments = $this->argumentResolver->getArguments($handler, 'handle', $messageData);
                $handler->handle(...$arguments);
            } catch (ArgumentResolverException $e) {
                $dispatcher->sendToUser($e->getMessage(), $sender);
            }
        }
    }

}
