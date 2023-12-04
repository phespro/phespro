<?php

namespace Phespro\ReactPHPServer;

use Phespro\Phespro\Http\Server\ServerConfiguration;
use Phespro\Phespro\Http\Server\ServerInterface;
use Phespro\Phespro\Kernel;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReactServer implements ServerInterface
{
    public function __construct(protected Kernel $kernel)
    {
    }

    public function getName(): string
    {
        return 'ReactPHP';
    }

    public function run(OutputInterface $output, ServerConfiguration $config): int
    {
        if (!extension_loaded('pcntl')) {
            $output->writeln('<error>Package pcntl not found.</error>');
            return Command::FAILURE;
        }

        if ($config->worker !== 1) {
            $output->writeln('<error>More than one worker is not implemented yet for ReactPHP');
            return Command::FAILURE;
        }

        $httpServer = new HttpServer(function(ServerRequestInterface $request) {
            return $this->kernel->handleWebRequest(false, $request);
        });

        $socketServer = new SocketServer($config->host);

        $httpServer->listen($socketServer);

        $output->writeln("<success>Server started on host http://{$config->host}</success>");

        Loop::addSignal(2, function() use ($output) {
            $output->writeln('<info>Server shutting down by command.</info>');
            Loop::stop();
        });

        Loop::run();

        return Command::SUCCESS;
    }

}