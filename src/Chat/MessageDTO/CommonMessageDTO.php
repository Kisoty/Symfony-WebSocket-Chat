<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageDTO;

use Symfony\Component\Validator\Constraints as Assert;

class CommonMessageDTO
{
    public function __construct(
        /**
         * @Assert\NotBlank()
         */
        public string $message
    ) {}
}
