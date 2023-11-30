<?php

namespace Phespro\Phespro\Http\Server;

use Phespro\Phespro\Kernel;

class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(
            StartServerCommand::class,
            fn() => new StartServerCommand($kernel->getByTag('http.server')),
            ['cli_command'],
        );
    }
}