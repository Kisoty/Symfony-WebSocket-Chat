<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\Receivers\ReceiverInterface;

interface MessageHandlerInterface
{
    /**
     * @param array $messageData
     * @param Chat $chat
     * @param ChatUser $sender
     * @param ReceiverInterface $receivers
     */
    public function __invoke(array $messageData, Chat $chat, ChatUser $sender, ReceiverInterface $receivers);
}
