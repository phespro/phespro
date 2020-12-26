<?php


namespace Phespro\Phespro\Tests;


use Laminas\Diactoros\ServerRequestFactory;
use League\Route\Router;
use Phespro\Container\Container;
use Phespro\Phespro\Kernel;
use Phespro\Phespro\PluginInterface;
use PHPUnit\Framework\TestCase;

class ErrorHandlerTestPlugin implements PluginInterface
{
    static function getPluginFactoryFunction(): callable
    {
        return fn() => new static;
    }

    function initializeContainer(Container $container): void
    {

    }

    function initializeWeb(Router $router): void
    {
        $router->get('/', function() {
            throw new \Exception('Testexception');
        });
    }
}

class ErrorHandlingTest extends TestCase
{
    /**
     * @throws \Phespro\Container\ServiceNotFoundException
     * @covers \Phespro\Phespro\ErrorHandlerMiddleware
     */
    public function test()
    {
        $kernel = new Kernel;
        $kernel->addPlugin(ErrorHandlerTestPlugin::class);
        $kernel->getContainer()->decorate('config', fn($container, $prev) => array_replace_recursive($prev(), [
            'debug' => [
                'displayErrorDetails' => true,
            ],
        ]));
        $response = $kernel->handleWebRequest(false, (new ServerRequestFactory)->createServerRequest('GET', '/'));
        $this->assertEquals(500, $response->getStatusCode());
    }
}