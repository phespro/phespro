<?php


namespace Phespro\Phespro\Migration\Commands;


use Phespro\Phespro\Migration\CliMigratorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ApplyAllCommand extends Command
{
    private CliMigratorInterface $cliMigrator;

    public function __construct(CliMigratorInterface $cliMigrator)
    {
        parent::__construct();
        $this->cliMigrator = $cliMigrator;
    }


    protected function configure()
    {
        $this->setName('migration:apply_all');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->cliMigrator->applyAll($input, $output);

        return 0;
    }

}