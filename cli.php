<?php

use League\Route\Router;
use Phespro\Phespro\Extensibility\AbstractExtension;
use Phespro\Phespro\Kernel;

require __DIR__ . '/vendor/autoload.php';



$kernel = new Kernel([
    new class extends AbstractExtension {

        function boot(Kernel $kernel): void
        {
            // TODO: Implement boot() method.
        }

        function bootHttp(Kernel $kernel, Router $router): void
        {
            $router->get('/', function() {
                $response = new \Laminas\Diactoros\Response();
                $response->getBody()->write('test');
                return $response;
            });
        }

    }
]);
$kernel->handleCli();