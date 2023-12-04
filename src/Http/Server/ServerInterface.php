<?php

namespace Phespro\Phespro\Http\Server;

use Psr\Log\LoggerInterface;

interface ServerInterface
{
    public function getName(): string;
    public function run(LoggerInterface $logger): int;
}