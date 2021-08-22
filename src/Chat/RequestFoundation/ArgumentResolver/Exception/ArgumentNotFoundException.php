<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\Exception;

class ArgumentNotFoundException extends ArgumentResolverException
{
    protected $message = 'Given parameter not found in message data';
}
