<?php

namespace Phespro\ReactPHPServer;

use Phespro\Phespro\Extensibility\AbstractExtension;
use Phespro\Phespro\Kernel;

class ReactExtension extends AbstractExtension
{
    function boot(Kernel $kernel): void
    {
        $kernel->add(
            ReactServer::class,
            fn() => new ReactServer($kernel),
            ['http.server'],
        );
    }
}