<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\ChatUser;

interface MessageHandlerInterface
{
    /**
     * @param array $messageData
     * @param Chat $chat
     * @param ChatUser $sender
     * @param ChatUser[] $receivers
     */
    public function handle(array $messageData, Chat $chat, ChatUser $sender, array $receivers);
}
