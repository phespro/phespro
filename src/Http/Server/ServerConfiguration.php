<?php

namespace Phespro\Phespro\Http\Server;

readonly class ServerConfiguration
{
    public function __construct(
        public string $host,
        public int $worker,
    )
    {
    }
}