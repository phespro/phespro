<?php

namespace Phespro\Phespro\Http\Middlewares;

use Laminas\Diactoros\Response;
use Phespro\Phespro\Security\Csrf\TokenProviderInterface;
use Phespro\Phespro\Security\Csrf\TokenValidatorInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

readonly class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(
        protected TokenProviderInterface  $csrfTokenProvider,
        protected TokenValidatorInterface $csrfTokenValidator,
    )
    {
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $method = strtoupper($request->getMethod());

        if ($method === 'GET') {
            return $handler->handle($request);
        }

        $expectedToken = $this->csrfTokenProvider->get();
        $parsedBody = $request->getParsedBody();

        $isOk = is_array($parsedBody) && $this->csrfTokenValidator->validate($expectedToken, $parsedBody['csrf_token'] ?? '');

        if ($isOk) {
            return $handler->handle($request);
        }

        return new Response(status: 403);
    }
}