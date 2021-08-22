<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\MessageDTO\CommonMessageDTO;
use Kisoty\WebSocketChat\Chat\Receivers\ReceiverInterface;

class SendMessageHandler
{
    public function handle(ChatUser $sender, ReceiverInterface $receivers, CommonMessageDTO $dto)
    {
        $receivers->receiveMessage($sender->getName() . ': ' . $dto->message);
    }
}
