<?php


namespace Phespro\Phespro\Assets;


/**
 * Phespro does not care, what exact technique you use for your asset management (e.g. webpack, gulp, assetic).
 * No matter what asset management technique you use, accessing the assets should always be the same.
 *
 * By implementing this interface, you can use your favorite asset management system in the background. This interface
 * is then accessed by the application for loading assets.
 *
 * Interface AssetLoaderInterface
 * @package Phespro\Phespro\Assets
 */
interface AssetLocatorInterface
{
    /**
     * Get the actual path of an asset.
     *
     * E.g. if asset versioning is in use, the argument `$path` could have the value '/js/script.js' and the return
     * value could be '/js/script_f4hs85.js'. This depends on the used AssetLocator implementation and the used asset
     * management system.
     *
     * @param string $path
     * @return string
     */
    function get(string $path): string;
}