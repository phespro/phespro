<?php


namespace Phespro\Phespro\Extensibility;



use League\Route\Router;
use Phespro\Phespro\Kernel;

abstract class AbstractExtension implements ExtensionInterface
{
    /**
     * @inheritDoc
     */
    static function preBoot(Kernel $kernel): void
    {
        $kernel->add(static::class, fn() => new static, ['extension']);
    }

    /**
     * @inheritDoc
     */
    function boot(Kernel $kernel): void
    {
    }

    /**
     * @inheritDoc
     */
    function bootHttp(Router $router): void
    {
    }
}
