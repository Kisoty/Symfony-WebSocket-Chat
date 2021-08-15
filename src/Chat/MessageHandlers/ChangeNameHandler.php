<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\MessageDTO\ChangeNameDTO;

class ChangeNameHandler implements MessageHandlerInterface
{

    /**
     * @inheritDoc
     */
    public function handle(array $messageData, Chat $chat, ChatUser $sender, array $receivers)
    {
        $messageDTO = new ChangeNameDTO($messageData['newName']);

        $sender->changeName($messageDTO->newName);
    }
}
