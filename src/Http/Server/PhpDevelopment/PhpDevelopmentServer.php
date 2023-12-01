<?php

namespace Phespro\Phespro\Http\Server\PhpDevelopment;

use Phespro\Phespro\Http\Server\ServerConfiguration;
use Phespro\Phespro\Http\Server\ServerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PhpDevelopmentServer implements ServerInterface
{
    public function getName(): string
    {
        return 'PhpDevelopment';
    }

    public function run(OutputInterface $output, ServerConfiguration $config): int
    {
        if (!extension_loaded('pcntl')) {
            $output->writeln('<error>Cannot start server, since ext-pcntl not loaded</error>');
            return Command::FAILURE;
        }

        if ($config->worker !== 1) {
            $output->writeln('<error>The php development server cannot run multiple worker.</error>');
            return Command::FAILURE;
        }

        $routerPath = __DIR__ . '/router.php';

        $output->writeln("<info>Run development server on {$config->host}</info>");

        // pcntl_exec is easier to use, since it does forward the output of the program;
        pcntl_exec('php', ['-S', $config->host, $routerPath]);
    }
}