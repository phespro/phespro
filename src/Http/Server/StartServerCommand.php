<?php

namespace Phespro\Phespro\Http\Server;

use Phespro\Phespro\Kernel;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StartServerCommand extends Command
{
    public function __construct(
        protected readonly Kernel $kernel,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('server:run');
        $this->addOption('host', null, InputOption::VALUE_REQUIRED, 'Host: e.g. "127.0.0.1:80"', '127.0.0.1:8080');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $httpServer = new HttpServer(function(ServerRequestInterface $request) {
            return $this->kernel->handleWebRequest(false, $request);
        });

        $host = $input->getOption('host');

        $socketServer = new SocketServer($host);

        $httpServer->listen($socketServer);

        $io = new SymfonyStyle($input, $output);

        $io->success("Server started on host http://$host");

        Loop::run();
    }
}