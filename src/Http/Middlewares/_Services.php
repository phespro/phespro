<?php

namespace Phespro\Phespro\Http\Middlewares;

use Phespro\Phespro\Kernel;
use Phespro\Phespro\Security\Csrf\TokenProviderInterface;
use Phespro\Phespro\Security\Csrf\TokenValidatorInterface;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(CsrfMiddleware::class, fn() => new CsrfMiddleware(
            $kernel->get(TokenProviderInterface::class),
            $kernel->get(TokenValidatorInterface::class),
        ));

        $kernel->add(AjaxOnlyMiddleware::class, fn() => new AjaxOnlyMiddleware);

    }
}