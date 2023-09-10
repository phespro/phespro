<?php

namespace Phespro\Phespro\NoTee;

use Laminas\Diactoros\Response;
use NoTee\NoTeeInterface;
use Psr\Http\Message\ResponseInterface;

trait NoTeeTrait
{
    public readonly NoTeeInterface $noTee;

    public function injectNoTee(NoTeeInterface $noTee): void
    {
        $this->noTee = $noTee;
    }

    public function renderResponse(string $templateFile, array $context = []): ResponseInterface
    {
        $response = new Response();
        $response->getBody()->write(
            $this->renderString($templateFile, $context),
        );
        return $response;
    }

    public function renderString(string $templateFile, array $context = []): string
    {
        return $this->noTee->render($templateFile, $context);
    }
}