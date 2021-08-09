<?php

namespace Phespro\Phespro\Tests;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use League\Route\Router;
use Phespro\Phespro\Extensibility\AbstractExtension;
use Phespro\Phespro\Http\AbstractAction;
use Phespro\Phespro\Http\ActionInterface;
use Phespro\Phespro\Kernel;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ContainerActionTestAction implements ActionInterface
{
    function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write('Hello Worlds');
        return $response;
    }
}

class ContainerActionTestExtension extends AbstractExtension
{
    function __construct()
    {

    }

    function boot(Kernel $kernel): void
    {
        $kernel->add(ContainerActionTestAction::class, fn() => new ContainerActionTestAction());
    }

    function bootHttp(Router $router): void
    {
        $router->get('/', ContainerActionTestAction::class);
    }
}

class ContainerActionTest extends TestCase
{
    function test()
    {
        $kernel = new Kernel([ContainerActionTestExtension::class]);
        $response = $kernel->handleWebRequest(
            false,
            (new ServerRequestFactory)->createServerRequest('GET', '/'),
        );
        $response->getBody()->rewind();
        $this->assertEquals('Hello Worlds', $response->getBody()->getContents());
    }
}