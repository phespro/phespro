<?php

namespace Phespro\Phespro\Assets;

use Phespro\Phespro\Kernel;

final class _Services
{
    public static function register(Kernel $kernel): void
    {
        $kernel->add(AssetLocatorInterface::class, fn() => new NoopAssetLocator);
    }
}