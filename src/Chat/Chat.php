<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat;

use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class Chat
{
    /**
     * [ connection_id => ChatUser ]
     * @var array<int, ChatUser>
     */
    private array $users = [];

    public function __construct(private Worker $worker) {}

    public function addUser(int $userId): void
    {
        $this->users[$userId] = new ChatUser($this, $userId, 'New user');
    }

    public function removeUser(int $userId): void
    {
        unset($this->users[$userId]);
    }

    public function sendToUser(string $data, ChatUser $user): void
    {
        foreach ($this->worker->connections as $establishedConnection) {
            if ($establishedConnection->id === $user->getId()) {
                $this->sendMessage($establishedConnection, $data);
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

    public function getUserById(int $userId): ?ChatUser
    {
        return $this->users[$userId] ?? null;
    }
}
