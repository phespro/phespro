<?php


namespace Phespro\Phespro;



use League\Route\Router;

interface PluginInterface
{
    function initializeWeb(Router $router): void;
}