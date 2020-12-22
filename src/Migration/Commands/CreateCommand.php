<?php


namespace Phespro\Phespro\Migration\Commands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCommand extends Command
{
    protected function configure()
    {
        $this->setName('migration:create');
        $this->setDescription('Create migration in directory');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = (new \DateTimeImmutable())->format('YmdHisu');

        $code = "<?php

namespace TBD; // TODO CHANGE

use Phespro\Phespro\MigrationInterface;

class Migration$id implements MigrationInterface 
{
    function getId(): string
    {
        return '$id';
    }
    
    function getDescription(): string
    {
        return ''; // TODO add description
    }
    
    function execute(): void
    {
        // TODO implement
    }
}
";

        $output->write($code);
    }
}