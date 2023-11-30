<?php

namespace Phespro\Phespro\Tests\Unit\Security\Csrf;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequestFactory;
use League\Route\Router;
use Phespro\Phespro\Kernel;
use Phespro\Phespro\Security\Csrf\MemoryTokenStorage;
use Phespro\Phespro\Security\Csrf\TokenStorageInterface;
use PHPUnit\Framework\TestCase;

class CsrfProtectionTest extends TestCase
{
    public function test()
    {
        $kernel = new Kernel;

        $kernel->decorate(TokenStorageInterface::class, fn() => new MemoryTokenStorage);

        $router = $kernel->get('router');
        assert($router instanceof Router);

        $router->post('/', fn() => new Response);

        $invalidRequest = ServerRequestFactory::fromGlobals(
            server: [
                'REQUEST_METHOD' => 'POST',
            ],
            body: [
                'csrf_token' => 'invalidToken',
            ],
        );

        $response = $kernel->handleWebRequest(false, $invalidRequest);


        $this->assertEquals(
            403,
            $response->getStatusCode(),
        );

        $validRequest = ServerRequestFactory::fromGlobals(
            server: [
                'REQUEST_METHOD' => 'POST',
            ],
            body: [
                'csrf_token' => $kernel->getObject(TokenStorageInterface::class)->get(),
            ],
        );

        $this->assertEquals(
            200,
            $kernel->handleWebRequest(false, $validRequest)->getStatusCode(),
        );
    }
}