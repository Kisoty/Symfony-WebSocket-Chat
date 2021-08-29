<?php

declare(strict_types=1);

namespace Kisoty\WebSocketChat\Chat\Command;

use Kisoty\WebSocketChat\Chat\MessageDispatcher;
use Kisoty\WebSocketChat\Chat\ChatUser;
use Kisoty\WebSocketChat\Chat\ChatUserInMemoryStorage;
use Kisoty\WebSocketChat\Chat\RequestFoundation\RequestProcessor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Workerman\Connection\TcpConnection;
use Workerman\Worker;

class ChatCommand extends Command
{
    public function __construct(
        private RequestProcessor $requestProcessor,
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
        $storage = new ChatUserInMemoryStorage();

        $dispatcher = new MessageDispatcher($worker);

        $worker->onConnect = function (TcpConnection $connection) use ($storage, $dispatcher) {
            echo "New connection $connection->id \n";
            $newUser = new ChatUser($dispatcher, $connection->id, 'New User');
            $storage->add($connection->id, $newUser);
        };

        $worker->onMessage = function (TcpConnection $connection, $data) use ($storage, $dispatcher) {
            $sender = $storage->getByConnectionId($connection->id);
            $this->requestProcessor->process($dispatcher, $sender, $data);
        };

        $worker->onClose = function (TcpConnection $connection) use ($storage) {
            echo "Connection $connection->id closed\n";
            $storage->remove($connection->id);
        };

        Worker::runAll();
    }
}
