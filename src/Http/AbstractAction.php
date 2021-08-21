<?php

namespace Phespro\Phespro\Http;

use Laminas\Diactoros\Response;
use NoTee\NoTeeInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractAction implements ActionInterface
{
    function __construct(protected NoTeeInterface $noTee)
    {
    }

    function render(string $noTeeFile, array $context = []): ResponseInterface
    {
        $response = new Response;
        $response->getBody()->write(
            (string)$this->noTee->render($noTeeFile, $context),
        );
        return $response;
    }
}