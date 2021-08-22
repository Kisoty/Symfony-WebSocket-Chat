<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\MessageDTO\ChangeNameDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangeNameHandler
{
    public function __construct(private ValidatorInterface $validator) {}

    public function handle(ChatUser $sender, ChangeNameDTO $dto)
    {
        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $errorMsg = '';
            foreach ($errors as $error) {
                $errorMsg .= $error->getMessage() . PHP_EOL;
            }

            $sender->receiveMessage($errorMsg);
        } else {
            $sender->changeName($dto->newName);
        }
    }
}
