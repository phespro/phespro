<?php

namespace Phespro\Phespro;

use Phespro\Container\ServiceNotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class LazyActionResolver
{
    public function __construct(protected ContainerInterface $container)
    {
        
    }

    /**
     * With this method you can create a wrapper function for callable action method services
     *
     * @param string $serviceTag
     * @return callable
     * @throws ServiceNotFoundException
     */
    public function wrapService(string $serviceTag): callable
    {
        if (!$this->container->has($serviceTag)) {
            throw new ServiceNotFoundException("The service '$serviceTag' does not exist.");
        }
        $container = $this->container;
        return function(ServerRequestInterface $request, array $args = []) use ($container, $serviceTag) : ResponseInterface {
            $service = $container->get($serviceTag);
            return $service($request, $args);
        };
    }
}
