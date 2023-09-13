<?php

namespace Phespro\Phespro\Configuration;

class FrameworkConfiguration
{
    public function __construct(
        public bool $displayErrorDetails,
        public bool $debugNoTee,
        public bool $autoCsrfProtect,
    )
    {
    }
}