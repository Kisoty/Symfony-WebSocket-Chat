<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver;

use Kisoty\WebSocketChat\Chat\MessageDTO\MessageDTOInterface;
use Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\Exception\ArgumentResolverException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class DTOResolver implements ArgumentResolverInterface
{
    public function __construct(private ValidatorInterface $validator) {}

    public function supports(string $argTypeName): bool
    {
        $reflection = new \ReflectionClass($argTypeName);

        if ($reflection->implementsInterface(MessageDTOInterface::class)) {
            return true;
        }

        return false;
    }

    /**
     * @throws ArgumentResolverException
     */
    public function resolve(array $messageData, string $argTypeName): object
    {
        $dto = new $argTypeName($messageData);

        $errors = $this->validator->validate($dto);

        if (count($errors) > 0) {
            $errorMsg = '';
            foreach ($errors as $error) {
                $errorMsg .= $error->getMessage() . PHP_EOL;
            }
            throw new ArgumentResolverException($errorMsg);
        }

        return $dto;
    }
}
