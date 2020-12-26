<?php


namespace Phespro\Phespro;



use League\Route\Router;
use Phespro\Container\Container;

interface PluginInterface
{
    static function getPluginFactoryFunction(): callable;
    function initializeContainer(Container $container): void;
    function initializeWeb(Router $router): void;
}
