<?php

namespace Phespro\Phespro;

abstract class ServiceProvider
{
    private function __construct()
    {
    }

    abstract public static function register(Kernel $kernel): void;
}