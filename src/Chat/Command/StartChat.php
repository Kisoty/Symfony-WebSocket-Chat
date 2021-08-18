<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\Command;

use Kisoty\WebSocketChat\Chat\Chat;
use Kisoty\WebSocketChat\Chat\MessageHandlers\MessageHandlerFactory;
use Kisoty\WebSocketChat\Chat\MessageParser\MessageParser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class StartChat extends Command
{
    public function __construct(
        private MessageParser $messageParser,
        private MessageHandlerFactory $factory,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected static $defaultName = 'chat';

    protected function configure()
    {
        $this
            ->setDescription('Starts new chat.')
            ->setHelp('This command starts new chat daemon.')
            ->addArgument('action', InputArgument::REQUIRED,
                'Workerman action. Accepts start, stop, restart, reload, status, connections actions.')
            ->addArgument('socket', InputArgument::REQUIRED);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $worker = new Worker($input->getArgument('socket'));
        $chat = new Chat($worker);

        $worker->onConnect = function (TcpConnection $connection) use ($chat) {
            echo "New connection $connection->id \n";
            $chat->addUser($connection->id);
        };

        $worker->onMessage = function (TcpConnection $connection, $data) use ($chat) {
            $method = $this->messageParser->getMethod($data);
            $messageData = $this->messageParser->getMessageData($data);
            $sender = $chat->getUserById($connection->id);
            $receivers = $this->messageParser->getReceiversFromChat($data, $chat);

            $handler = $this->factory->getHandler($method);

            $handler($messageData, $chat, $sender, $receivers);
        };

        $worker->onClose = function (TcpConnection $connection) use ($chat) {
            echo "Connection $connection->id closed\n";
            $chat->removeUser($connection->id);
        };

        Worker::runAll();
    }
}
