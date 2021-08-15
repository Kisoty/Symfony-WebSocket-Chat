<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat;

use Kisoty\WebSocketChat\Chat\MessageHandlers\MessageHandlerFactory;
use Kisoty\WebSocketChat\Chat\MessageParser\MessageParser;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class Chat
{
    private Worker $worker;
    private MessageParser $messageParser;

    /**
     * @var \SplObjectStorage
     */
    private \SplObjectStorage $users;

    public function __construct(string $socketName)
    {
        $this->initWorker($socketName);
        $this->users = new \SplObjectStorage();
        $this->messageParser = new MessageParser();
    }

    private function initWorker(string $socketName): void
    {
        $this->worker = new Worker($socketName);

        $this->worker->onConnect = function (TcpConnection $connection) {
            echo "New connection $connection->id \n";
            $this->users->attach(new ChatUser($connection->id, 'New user'));
        };

        $this->worker->onMessage = function (TcpConnection $connection, $data) {
            $method = $this->messageParser->getMethod($data);
            $messageData = $this->messageParser->getMessageData($data);
            $sender = $this->getUserById($connection->id);
            $receivers = [];

            if (empty($this->messageParser->getOutputReceivers($data))) {
                $receivers = ['*'];
            } else {
                array_map(function ($val) use (&$receivers) {
                    $receivers[] = $this->getUserById($val);
                }, $this->messageParser->getOutputReceivers($data));
            }

            $handler = (new MessageHandlerFactory())->getHandler($method);

            $handler->handle($messageData, $this, $sender, $receivers);
        };

        $this->worker->onClose = function (TcpConnection $connection) {
            echo "Connection $connection->id closed\n";

            $this->users->detach($this->getUserById($connection->id));
        };
    }

    public function sendToUser(string $data, ChatUser $user): void
    {
        foreach ($this->worker->connections as $connection) {
            if ($connection->id === $user->getId()) {
                $this->sendMessage($connection, $data);
            }
        }
    }

    public function sendToAll(string $data): void
    {
        foreach ($this->worker->connections as $establishedConnection) {
            $this->sendMessage($establishedConnection, $data);
        }
    }

    private function sendMessage(TcpConnection $connection, string $data): void
    {
        $connection->send($data);
    }

    private function getUserById(int $id): ?ChatUser
    {
        /* @var ChatUser $user */
        foreach ($this->users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }
        return null;
    }

    public function start(): void
    {
        Worker::runAll();
    }

}
