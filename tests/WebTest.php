<?php


namespace Phespro\Phespro\Tests;


use Exception;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use League\Route\Router;
use Phespro\Phespro\Extensibility\AbstractExtension;
use Phespro\Phespro\Kernel;
use PHPUnit\Framework\TestCase;

class TestPlugin extends AbstractExtension
{
    function bootHttp(Router $router): void
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
     * @throws Exception
     * @covers \Phespro\Phespro\Kernel
     */
    public function test()
    {
        $kernel = new Kernel([TestPlugin::class]);

        $response = $kernel->handleWebRequest(
            false,
            (new ServerRequestFactory)->createServerRequest('GET', '/'),
        );
        $this->assertEquals(200, $response->getStatusCode());
        $response->getBody()->rewind();
        $this->assertEquals('Hello World', $response->getBody()->getContents());
    }
}