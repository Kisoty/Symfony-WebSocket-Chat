<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\RequestFoundation\MessageParser;

use Kisoty\WebSocketChat\Chat\MessageDispatcher;
use Kisoty\WebSocketChat\Chat\ChatUserInMemoryStorage;
use Kisoty\WebSocketChat\Chat\Receivers\AllChatUsers;
use Kisoty\WebSocketChat\Chat\Receivers\ChatUserBatch;
use Kisoty\WebSocketChat\Chat\Receivers\ReceiverInterface;

class MessageParser
{
    private array $messageData = [];

    public function __construct(private ChatUserInMemoryStorage $storage) {}

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

    public function getReceiversFromChat(MessageDispatcher $dispatcher): ReceiverInterface
    {
        if (!isset($this->messageData['receivers']) || empty($this->messageData['receivers'])) {
            $receivers = new AllChatUsers($dispatcher);
        } else {
            $receiverUsers = [];

            foreach ($this->messageData['receivers'] as $receiverId) {
                $receiverUsers[] = $this->storage->getByConnectionId($receiverId);
            }
            $receivers = new ChatUserBatch($dispatcher, $receiverUsers);
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
