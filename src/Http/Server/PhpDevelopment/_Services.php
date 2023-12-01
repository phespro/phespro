<?php

namespace Phespro\Phespro\Http\Server\PhpDevelopment;

use Phespro\Phespro\Kernel;
use Phespro\Phespro\ServiceProvider;

class _Services extends ServiceProvider
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(PhpDevelopmentServer::class, fn() => new PhpDevelopmentServer, ['http.server']);
    }
}