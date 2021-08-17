<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat;

use Kisoty\WebSocketChat\Chat\MessageHandlers\MessageHandlerFactory;
use Kisoty\WebSocketChat\Chat\MessageParser\MessageParser;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class Chat
{
    private Worker $worker;
    private MessageParser $messageParser;

    /**
     * [ connection_id => ChatUser ]
     * @var array<int, ChatUser>
     */
    private array $users = [];

    public function __construct(string $socketName, private ContainerInterface $container)
    {
        $this->initWorker($socketName);
        $this->messageParser = new MessageParser();
    }

    private function initWorker(string $socketName): void
    {
        $this->worker = new Worker($socketName);

        $this->worker->onConnect = function (TcpConnection $connection) {
            echo "New connection $connection->id \n";
            $this->users[$connection->id] = new ChatUser($connection->id, 'New user');
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

            $handler = (new MessageHandlerFactory($this->container))->getHandler($method);

            $handler->handle($messageData, $this, $sender, $receivers);
        };

        $this->worker->onClose = function (TcpConnection $connection) {
            echo "Connection $connection->id closed\n";

            unset($this->users[$connection->id]);
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
        return $this->users[$id] ?? null;
    }

    public function start(): void
    {
        Worker::runAll();
    }

}
