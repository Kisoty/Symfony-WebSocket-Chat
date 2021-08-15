<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\MessageDTO\CommonMessageDTO;

class SendMessageHandler implements MessageHandlerInterface
{
    /**
     * @param array $messageData
     * @param Chat $chat
     * @param ChatUser $sender
     * @param array|ChatUser[] $receivers
     */
    public function handle(array $messageData, Chat $chat, ChatUser $sender, array $receivers)
    {
        $messageDTO = new CommonMessageDTO($messageData['message']);

        if ($receivers === ['*']) {
            $chat->sendToAll($sender->getName() . ': ' . $messageDTO->message);
        } else {
            array_map(function ($receiver) use($messageDTO, $chat, $sender) {
                $chat->sendToUser($sender->getName() . ': ' . $messageDTO->message, $receiver);
            }, $receivers);
        }
    }
}
