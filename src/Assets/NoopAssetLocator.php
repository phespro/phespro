<?php


namespace Phespro\Phespro\Assets;


class NoopAssetLocator implements AssetLocatorInterface
{
    function get(string $path): string
    {
        return $path;
    }
}