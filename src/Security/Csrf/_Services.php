<?php

namespace Phespro\Phespro\Security\Csrf;

use Phespro\Phespro\Kernel;
use Psr\Container\ContainerInterface;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(TokenValidatorInterface::class, fn() => new TokenValidator);
        $kernel->add(TokenStorageInterface::class, fn() => new PhpSessionTokenStorage);
        $kernel->add(TokenGeneratorInterface::class, fn() => new TokenGenerator);
        $kernel->add(TokenProviderInterface::class, fn() => new TokenProvider(
            $kernel->getObject(TokenGeneratorInterface::class),
            $kernel->getObject(TokenStorageInterface::class),
        ));
    }
}