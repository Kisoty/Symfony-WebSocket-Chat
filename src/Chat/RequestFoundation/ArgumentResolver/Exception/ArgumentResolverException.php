<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\RequestFoundation\ArgumentResolver\Exception;

class ArgumentResolverException extends \Exception
{
    protected $message = 'Error while getting handler arguments in message data';
}
