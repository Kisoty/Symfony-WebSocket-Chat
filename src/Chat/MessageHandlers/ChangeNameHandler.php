<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\MessageDTO\ChangeNameDTO;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ChangeNameHandler implements MessageHandlerInterface
{
    public function __construct(private ValidatorInterface $validator) {}

    /**
     * @inheritDoc
     */
    public function handle(array $messageData, Chat $chat, ChatUser $sender, array $receivers)
    {
        $messageDTO = new ChangeNameDTO($messageData['newName']);

        $errors = $this->validator->validate($messageDTO);

        if (count($errors) > 0) {
            $errorMsg = '';
            foreach ($errors as $error) {
                $errorMsg .= $error->getMessage() . PHP_EOL;
            }

            $chat->sendToUser($errorMsg, $sender);
        } else {
            $sender->changeName($messageDTO->newName);
        }
    }
}
