<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\MessageParser;

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
     * @return array|int[]
     */
    public function getOutputReceivers(string $message): array
    {
        $messageData = json_decode($message, true);

        return $messageData['receivers'] ?? [];
    }

    public function getMessageData(string $message): array
    {
        $messageData = json_decode($message, true);

        return $messageData['data'] ?? [];
    }

}
