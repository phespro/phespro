<?php

namespace Phespro\Phespro\Security;

use Phespro\Phespro\Kernel;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        Csrf\_Services::register($kernel);
    }
}