<?php

namespace Phespro\Phespro\Http;

use Phespro\Phespro\Kernel;
use Psr\Log\LoggerInterface;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(
            WebRequestErrorHandlerInterface::class,
            fn() => new WebRequestErrorHandler(
                $kernel->get(LoggerInterface::class),
                $kernel->get('config'),
            )
        );

        Middlewares\_Services::register($kernel);
    }
}