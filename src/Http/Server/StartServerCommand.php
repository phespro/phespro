<?php

namespace Phespro\Phespro\Http\Server;

use Psr\Log\LogLevel;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StartServerCommand extends Command
{
    /**
     * @param ServerInterface[] $server
     */
    public function __construct(
        protected readonly array $server,
    )
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('server:run');

        $names = array_map(fn(ServerInterface $server) => $server->getName(), $this->server);
        $names = implode(', ', $names);
        $this->addOption(
            'server',
            's',
            InputOption::VALUE_REQUIRED,
            "What type of server do you want to start? ($names)",
            'PhpDevelopment',
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $serverInput = $input->getOption('server');
        $filteredServers = array_filter($this->server, fn(ServerInterface $server) => $server->getName() === $serverInput);

        if (count($filteredServers) === 0) {
            $io = new SymfonyStyle($input, $output);
            $io->error("Server $serverInput not found.");
            return self::FAILURE;
        }

        $server = $this->server[array_key_first($filteredServers)];

        $server->run(new ConsoleLogger($output));

        return self::SUCCESS;
    }
}