<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\RequestFoundation\MessageParser;

class WrongMessageFormatException extends \Exception
{
    protected $message = 'Wrong message format.';
}
