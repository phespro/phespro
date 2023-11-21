<?php

namespace Phespro\Phespro\Configuration;

use Phespro\Phespro\Kernel;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add('config', fn() => new FrameworkConfiguration(
            displayErrorDetails: getenv('PHESPRO_DISPLAY_ERROR_DETAILS') ?: false,
            debugNoTee: getenv('PHESPRO_DEBUG_NOTEE') ?: false,
            autoCsrfProtect: getenv('PHESPRO_AUTO_CSRF_PROTECT') ?: true,
        ));
    }
}