<?php


namespace Phespro\Phespro\Tests;


use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use Phespro\Container\Container;
use Phespro\Phespro\LazyActionResolver;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class LazyActionResolverTest extends TestCase
{
    /**
     * @throws \Phespro\Container\ServiceAlreadyDefinedException
     * @throws \Phespro\Container\ServiceNotFoundException
     * @covers \Phespro\Phespro\LazyActionResolver
     */
    public function test()
    {
        $container = new Container;
        $lazyActionResolver = new LazyActionResolver($container);
        $container->add('testFun', fn() => function(ServerRequestInterface $request) {
            $response = new Response;
            $response->getBody()->write('Hello World');
            return $response;
        });
        $callable = $lazyActionResolver->wrapService('testFun');
        $response = $callable((new ServerRequestFactory)->createServerRequest('GET', '/'));
        assert($response instanceof Response);
        $response->getBody()->rewind();
        $this->assertEquals('Hello World', $response->getBody()->getContents());
    }
}