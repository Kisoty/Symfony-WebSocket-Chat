<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\MessageDTO\CommonMessageDTO;
use Kisoty\WebSocketChat\Chat\Receivers\ReceiverInterface;

class SendMessageHandler implements MessageHandlerInterface
{
    /**
     * @param array $messageData
     * @param Chat $chat
     * @param ChatUser $sender
     * @param ReceiverInterface $receivers
     */
    public function __invoke(array $messageData, Chat $chat, ChatUser $sender, ReceiverInterface $receivers)
    {
        $messageDTO = new CommonMessageDTO($messageData['message']);

        $receivers->receiveMessage($chat, $sender->getName() . ': ' . $messageDTO->message);
    }
}
