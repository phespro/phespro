<?php

namespace Phespro\Phespro\Http\Middlewares;

use Laminas\Diactoros\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AjaxOnlyMiddleware implements MiddlewareInterface
{
    protected const HEADER_NAME = 'HTTP_X_REQUESTED_WITH';
    protected const EXPECTED_HEADER_VALUE = 'XMLHttpRequest';

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $header = $request->getHeader(self::HEADER_NAME);
        $isAjax = !empty($header) && $header[array_key_first($header)] === self::EXPECTED_HEADER_VALUE;
        if ($isAjax) {
            return $handler->handle($request);
        } else {
            $response = new Response(status: 400);
            $response->getBody()->write('Only AJAX requests are allowed for this method');
            return $response;
        }
    }
}