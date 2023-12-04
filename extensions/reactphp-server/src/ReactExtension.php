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
            fn() => new ReactServer($kernel, $kernel->getObject(Config::class)),
            ['http.server'],
        );

        $kernel->add(Config::class, fn() => new Config(
            getenv('PHESPRO_REACT_HOST') ?: '0.0.0.0:80',
            getenv('PHESPRO_REACT_WORKER') ?: 1,
        ));
    }
}