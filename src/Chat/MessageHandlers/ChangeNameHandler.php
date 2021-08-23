<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\MessageDTO\ChangeNameDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangeNameHandler
{
    public function __construct() {}

    public function handle(ChatUser $sender, ChangeNameDTO $dto)
    {
        $sender->changeName($dto->newName);
    }
}
