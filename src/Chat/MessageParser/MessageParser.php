<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\MessageParser;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\Receivers\AllChatUsers;
use Kisoty\WebSocketChat\Chat\Receivers\ChatUserBatch;
use Kisoty\WebSocketChat\Chat\Receivers\ReceiverInterface;

class MessageParser
{
    /**
     * @throws WrongMessageFormatException
     */
    public function getMethod(string $message): string
    {
        $messageData = json_decode($message, true);

        if (!isset($messageData['method'])) {
            throw new WrongMessageFormatException('Method not specified.');
        }

        return $messageData['method'];
    }

    /**
     * @return ReceiverInterface
     */
    public function getReceiversFromChat(string $message, Chat $chat): ReceiverInterface
    {
        $messageData = json_decode($message, true);

        if (!isset($messageData['receivers']) || empty($messageData['receivers'])) {
            $receivers = new AllChatUsers();
        } else {
            $receiverUsers = [];

            foreach ($messageData['receivers'] as $receiverId) {
                $receiverUsers[] = $chat->getUserById($receiverId);
            }
            $receivers = new ChatUserBatch($receiverUsers);
        }

        return $receivers;
    }

    public function getMessageData(string $message): array
    {
        $messageData = json_decode($message, true);

        return $messageData['data'] ?? [];
    }
}
