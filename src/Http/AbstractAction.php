<?php

namespace Phespro\Phespro\Http;

use Laminas\Diactoros\Response;
use NoTee\NoTeeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractAction implements ActionInterface
{
    private function __construct(protected NoTeeInterface $noTee)
    {
    }

    function render(string $noTeeFile, array $context): ResponseInterface
    {
        return new Response(
            (string)$this->noTee->render($noTeeFile, $context)
        );
    }
}