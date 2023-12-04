<?php

namespace Phespro\ReactPHPServer;

use Phespro\Phespro\Http\Server\ServerConfiguration;
use Phespro\Phespro\Http\Server\ServerInterface;
use Phespro\Phespro\Kernel;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReactServer implements ServerInterface
{
    public function __construct(protected Kernel $kernel, protected Config $config)
    {
    }

    public function getName(): string
    {
        return 'ReactPHP';
    }

    public function run(LoggerInterface $logger): int
    {
        if (!extension_loaded('pcntl')) {
            $logger->error('Package pcntl not found.');
            return Command::FAILURE;
        }

        $httpServer = new HttpServer(function(ServerRequestInterface $request) use ($logger) {
            $logger->info('Incoming Web Request from');
            return $this->kernel->handleWebRequest(false, $request);
        });

        $socketServer = new SocketServer($this->config->host, [

        ]);

        $httpServer->listen($socketServer);

        $logger->info("Server started on host http://{$this->config->host}");

        Loop::addSignal(2, function() use ($logger) {
            $logger->info('Server shutting down by command.');
            Loop::stop();
        });

        Loop::run();

        return Command::SUCCESS;
    }

}