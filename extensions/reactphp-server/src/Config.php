<?php

namespace Phespro\ReactPHPServer;

class Config
{
    public function __construct(
        public string $host,
        public int $workerAmount,
    )
    {
    }
}