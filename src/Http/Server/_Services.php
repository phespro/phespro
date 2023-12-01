<?php

namespace Phespro\Phespro\Http\Server;

use Phespro\Phespro\Kernel;
use Phespro\Phespro\ServiceProvider;

class _Services extends ServiceProvider
{
    public static function register(Kernel $kernel): void
    {
        PhpDevelopment\_Services::register($kernel);

        $kernel->add(
            StartServerCommand::class,
            fn() => new StartServerCommand($kernel->getByTag('http.server')),
            ['cli_command'],
        );
    }
}