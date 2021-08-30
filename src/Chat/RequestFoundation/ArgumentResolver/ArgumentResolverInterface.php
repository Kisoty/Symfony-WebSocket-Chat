<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver;

use Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\Exception\ArgumentResolverException;

interface ArgumentResolverInterface
{
    public function supports(\ReflectionParameter $parameter): bool;

    /**
     * @throws ArgumentResolverException
     */
    public function resolve(array $messageData, \ReflectionParameter $parameter): object;
}
