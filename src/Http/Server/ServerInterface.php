<?php

namespace Phespro\Phespro\Http\Server;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface ServerInterface
{
    public function run(InputInterface $input, OutputInterface $output, ServerConfiguration $config): int;
}