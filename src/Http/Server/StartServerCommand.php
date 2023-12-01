<?php

namespace Phespro\Phespro\Http\Server;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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

        $this->addOption('host', null, InputOption::VALUE_REQUIRED, 'Host: e.g. "127.0.0.1:80"', '127.0.0.1:8080');
        $this->addOption('workers', 'w', InputOption::VALUE_REQUIRED, 'Number of workers', 1);
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

        $server->run($output, new ServerConfiguration(
            host: $input->getOption('host'),
            worker: (int)$input->getOption('workers'),
        ));

        return self::SUCCESS;
    }
}