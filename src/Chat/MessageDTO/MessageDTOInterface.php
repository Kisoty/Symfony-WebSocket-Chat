<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageDTO;

interface MessageDTOInterface
{
    public function __construct(array $messageData);
}
