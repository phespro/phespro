<?php

namespace Phespro\Phespro\Configuration;

readonly class FrameworkConfiguration
{
    public function __construct(
        public bool $displayErrorDetails = false,
        public bool $debugNoTee = false,
    )
    {
    }
}