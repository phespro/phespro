<?php

namespace Phespro\Phespro\NoTee;

use NoTee\NoTee;
use NoTee\NoTeeInterface;
use Phespro\Container\Container;
use Phespro\Phespro\Assets\AssetLocatorInterface;
use Phespro\Phespro\Configuration\FrameworkConfiguration;
use Phespro\Phespro\Kernel;
use Phespro\Phespro\Security\Csrf\NoTeeSubscriber;
use Phespro\Phespro\Security\Csrf\TokenProviderInterface;
use Psr\Container\ContainerInterface;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(
            NoTeeInterface::class,
            function(Kernel $c) {
                $config = $c->get('config');
                assert($config instanceof FrameworkConfiguration);

                $noTee = NoTee::create(
                    templateDirs: $c->get('template_dirs'),
                    defaultContext: $c->get('template_context'),
                    debug: $config->debugNoTee,
                );

                if ($config->autoCsrfProtect) {
                    $noTee->getNodeFactory()->subscribe($c->getObject(NoTeeSubscriber::class));
                }

                return $noTee;
            }
        );

        $kernel->add(
            'template_dirs',
            fn() => [],
        );

        $kernel->add(
            'template_context',
            fn(Container $c) => [
                'asset' => fn(string $path) => $c->getObject(AssetLocatorInterface::class)->get($path),
            ],
        );

        $kernel->add(NoTeeSubscriber::class, fn() => new NoTeeSubscriber(
            $kernel->getObject(TokenProviderInterface::class),
        ));

        $kernel->decorateAll(function(Container $c, mixed $inner) {
            if (is_object($inner) && method_exists($inner, 'injectNoTee')) {
                $inner->injectNoTee($c->getObject(NoTeeInterface::class));
            }
            return $inner;
        });
    }
}