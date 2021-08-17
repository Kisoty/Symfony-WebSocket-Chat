<?php

declare(strict_types=1);


namespace Kisoty\WebSocketChat\Chat\Command;


use Kisoty\WebSocketChat\Chat\Chat;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StartChat extends Command
{
    public function __construct(private ContainerInterface $container, string $name = null)
    {
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
        $chat = new Chat($input->getArgument('socket'), $this->container);

        $chat->start();
    }
}
