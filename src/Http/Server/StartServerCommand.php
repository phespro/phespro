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
        $types = array_map(fn(Type $type) => $type->value, Type::cases());
        $types = implode(', ', $types);

        $this->setName('server:run');
        $this->addOption(
            'type',
            't',
            InputOption::VALUE_REQUIRED,
            "What type of server do you want to start? ($types)",
            Type::PHPDEV->value,
        );
        $this->addOption('host', null, InputOption::VALUE_REQUIRED, 'Host: e.g. "127.0.0.1:80"', '127.0.0.1:8080');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $type = $input->getOption('type');
        $type = Type::from($type);

        $io = new SymfonyStyle($input, $output);

        return self::SUCCESS;
    }
}