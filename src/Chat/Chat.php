<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat;

use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class Chat
{
    private Worker $worker;

    /**
     * @var \SplObjectStorage
     */
    private \SplObjectStorage $users;

    public function __construct(string $socketName)
    {
        $this->initWorker($socketName);
        $this->users = new \SplObjectStorage();
    }

    private function initWorker(string $socketName): void
    {
        $this->worker = new Worker($socketName);

        $this->worker->onConnect = function (TcpConnection $connection) {
            echo "New connection $connection->id \n";
            $this->users->attach(new ChatUser($connection->id, 'New user'));
        };
        $this->worker->onMessage = function (TcpConnection $connection, $data) {
            if (str_contains($data, 'getusers')) {
                $this->sendMessage($connection, print_r($this->users, true));
            } elseif (str_contains($data, 'changeName: ')) {
                $newUserName = str_replace('changeName: ', '', $data);
                $this->getUserById($connection->id)->changeName($newUserName);
                $this->sendMessage($connection, 'Name changed to ' . $newUserName);
            } else {
                $data = $this->getUserById($connection->id)->getName() . ': ' . $data;
                $this->sendToAll($data);
            }
        };
        $this->worker->onClose = function (TcpConnection $connection) {
            echo "Connection $connection->id closed\n";

            $this->users->detach($this->getUserById($connection->id));
        };
    }

    private function sendToAll(string $data): void
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
