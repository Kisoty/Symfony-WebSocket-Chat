<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\MessageParser;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\Receivers\AllChatUsers;
use Kisoty\WebSocketChat\Chat\Receivers\ChatUserBatch;
use Kisoty\WebSocketChat\Chat\Receivers\ReceiverInterface;

class MessageParser
{
    private array $messageData = [];

    /**
     * @throws WrongMessageFormatException
     */
    public function setMessage(string $message): void
    {
        $messageData = json_decode($message, true);

        if ($messageData === null) {
            throw new WrongMessageFormatException();
        }
        $this->messageData = $messageData;
    }

    /**
     * @throws WrongMessageFormatException
     */
    public function getMethod(): string
    {
        if (!isset($this->messageData['method'])) {
            throw new WrongMessageFormatException('Method not specified.');
        }

        return $this->messageData['method'];
    }

    public function getReceiversFromChat(Chat $chat): ReceiverInterface
    {
        if (!isset($this->messageData['receivers']) || empty($this->messageData['receivers'])) {
            $receivers = new AllChatUsers($chat);
        } else {
            $receiverUsers = [];

            foreach ($this->messageData['receivers'] as $receiverId) {
                $receiverUsers[] = $chat->getUserById($receiverId);
            }
            $receivers = new ChatUserBatch($chat, $receiverUsers);
        }
        return $receivers;
    }

    /**
     * @throws WrongMessageFormatException
     */
    public function getMessageData(): array
    {
        if (!isset($this->messageData['data'])) {
            throw new WrongMessageFormatException('Method not specified.');
        }

        return $this->messageData['data'];
    }
}
