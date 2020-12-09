<?php


namespace Phespro\Phespro\Migration;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface CliMigratorInterface
{
    function applyAll(InputInterface $input, OutputInterface $output): void;
}