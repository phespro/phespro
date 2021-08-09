<?php


namespace Phespro\Phespro\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * This error handler is responsible for managing the case of an error.
 *
 * Interface WebRequestErrorHandlerInterface
 * @package Phespro\Phespro
 */
interface WebRequestErrorHandlerInterface
{
    function handle(\Throwable $err): ResponseInterface;
}
