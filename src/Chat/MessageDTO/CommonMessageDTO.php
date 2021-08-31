<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageDTO;

use Symfony\Component\Validator\Constraints as Assert;

class CommonMessageDTO implements MessageDTOInterface
{
    #[Assert\NotBlank]
    public string $message;

    public function __construct(array $messageData)
    {
        $this->message = (string)$messageData['message'] ?? '';
    }
}
