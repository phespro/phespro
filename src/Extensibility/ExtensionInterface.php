<?php

namespace Phespro\Phespro\Extensibility;

use Phespro\Container\ServiceAlreadyDefinedException;
use League\Route\Router;
use Phespro\Phespro\Kernel;

interface ExtensionInterface
{
    /**
     * Phespro approach is, to use the dependency injection container for everything. This also includes extensions.
     *
     * Therefore, we need a possibility, to add services (especially the extensions) to the container, before the
     * extensions are initialized by the kernel.
     *
     * You should override this method, if your extensions requires arguments in the constructor (dependency injection).
     *
     * @param Kernel $kernel
     * @throws ServiceAlreadyDefinedException
     */
    static function preBoot(Kernel $kernel): void;

    /**
     * After all extensions were registered (preBoot) the actual boot is executed. In the order in which extensions
     * were registered, the kernel creates an instance of the extensions and calls the boot method.
     *
     * Normally you should register services in this method.
     *
     * @param Kernel $kernel
     */
    function boot(Kernel $kernel): void;

    /**
     * This method can be used, to add routes and middlewares to the router
     *
     * @param Router $router
     */
    function bootHttp(Router $router): void;
}