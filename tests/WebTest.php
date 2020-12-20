<?php


namespace Phespro\Phespro\Tests;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use League\Route\Router;
use Phespro\Container\Container;
use Phespro\Phespro\Kernel;
use Phespro\Phespro\PluginInterface;
use PHPUnit\Framework\TestCase;

class TestPlugin implements PluginInterface
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
            $response = new Response;
            $response->getBody()->write('Hello World');
            return $response;
        });
    }
}

class WebTest extends TestCase
{
    /**
     * @throws \Exception
     * @covers \Phespro\Phespro\Kernel
     */
    public function test()
    {
        $kernel = new Kernel([]);

        $kernel->addPlugin(TestPlugin::class);

        $response = $kernel->handleWebRequest(
            false,
            (new ServerRequestFactory)->createServerRequest('GET', '/'),
        );
        $this->assertEquals(200, $response->getStatusCode());
        $response->getBody()->rewind();
        $this->assertEquals('Hello World', $response->getBody()->getContents());
    }
}