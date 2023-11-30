<?php

namespace Phespro\ReactPHPExtension;

use Phespro\Phespro\Extensibility\AbstractExtension;
use Phespro\Phespro\Kernel;

class ReactPHPExtension extends AbstractExtension
{
    function boot(Kernel $kernel): void
    {
        $kernel->add(
            'http.server.reactphp',
            fn() => new Server,
            ['http.server'],
        );
    }
}