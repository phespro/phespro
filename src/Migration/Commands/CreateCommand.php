<?php


namespace Phespro\Phespro\Migration\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
    protected function configure()
    {
        $this->setName('migration:create');
        $this->setDescription('Create migration in directory');

        $this->addOption('directory', 'd', InputOption::VALUE_REQUIRED, 'Where should the migration file be created?', getcwd());
        $this->addOption('namespace', 's', InputOption::VALUE_REQUIRED, 'Namespace of the generated class');
        $this->addOption('description', 'e', InputOption::VALUE_OPTIONAL, 'Description of the migration', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getOption('directory');
        $namespace = $input->getOption('namespace');
        $description = $input->getOption('description');
        $description = str_replace("'", '\\\'', $description);
        $id = (new \DateTimeImmutable())->format('YmdHisu') . random_int(0, 9) . random_int(0, 9) . random_int(0, 9);
        $className = "Migration$id";

        $code = "<?php

namespace $namespace;

use Phespro\Phespro\Migration\MigrationInterface;

class $className implements MigrationInterface 
{
    function getId(): string
    {
        return '$id';
    }
    
    function getDescription(): string
    {
        return '$description';
    }
    
    function execute(): void
    {
        // TODO implement
    }
}
";

        file_put_contents($directory . "/$className.php", $code);

        return 0;
    }
}
