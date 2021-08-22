<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\MessageDTO;

use Symfony\Component\Validator\Constraints as Assert;

class ChangeNameDTO implements MessageDTOInterface
{
    /**
     * @Assert\NotBlank()
     * @Assert\Length(
     *     min=2,
     *     max=10,
     *     minMessage = "Your new name must be at least {{ limit }} characters long",
     *     maxMessage = "Your new name cannot be longer than {{ limit }} characters"
     * )
     */
    public string $newName;

    public function __construct(array $messageData) {
        $this->newName = $messageData['newName'] ?? '';
    }
}
