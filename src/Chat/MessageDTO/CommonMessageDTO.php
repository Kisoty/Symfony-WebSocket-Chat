<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageDTO;

use Symfony\Component\Validator\Constraints as Assert;

class CommonMessageDTO
{
    /**
     * @Assert\NotBlank()
     */
    public string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }
}
