<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\MessageHandlers;

class HandlerNotFoundException extends \Exception
{
    protected $message = 'Handler was not found';
}
