<?php

namespace Phespro\ReactPHPExtension;

use Phespro\Phespro\Http\Server\ServerConfiguration;
use Phespro\Phespro\Http\Server\ServerInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\EventLoop\Loop;
use React\Http\HttpServer;
use React\Socket\SocketServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Server implements ServerInterface
{
    public function run(InputInterface $input, OutputInterface $output, ServerConfiguration $config): int
    {
        $io = new SymfonyStyle($input, $output);

        if (!extension_loaded('pcntl')) {
            $io->error('Package pcntl not found.');
            return Command::FAILURE;
        }

        $httpServer = new HttpServer(function(ServerRequestInterface $request) {
            return $this->kernel->handleWebRequest(false, $request);
        });

        $host = $input->getOption('host');

        $socketServer = new SocketServer($host);

        $httpServer->listen($socketServer);

        $io->success("Server started on host http://$host");

        Loop::addSignal(2, function() use ($io) {
            $io->info('Server shutting down by command.');
            Loop::stop();
        });

        Loop::run();

        return Command::SUCCESS;
    }

}