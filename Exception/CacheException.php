<?php

namespace Box\Component\Console\Exception;

use RuntimeException;

/**
 * Thrown for container cache related issues.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @codeCoverageIgnore
 */
class CacheException extends RuntimeException
{
    /**
     * Creates a new exception for when a cache is determined to be stale.
     *
     * @param string $file The cache file.
     *
     * @return CacheException The new exception.
     */
    public static function cacheStale($file)
    {
        return new self(
            "The cache file \"$file\" is stale."
        );
    }

    /**
     * Creates a new exception for when a directory could not be created.
     *
     * @param string $dir The cache directory path.
     *
     * @return CacheException The new exception.
     */
    public static function cannotCreateDir($dir)
    {
        return new self(
            "The cache directory \"$dir\" could not be created."
        );
    }

    /**
     * Creates a new exception for when a class does not exist in the file.
     *
     * @param string $class The name of the class.
     * @param string $file  The path to the cache file.
     *
     * @return CacheException The new exception.
     */
    public static function classNotExist($class, $file)
    {
        return new self(
            "The container class \"$class\" does not exist in the cache file \"$file\"."
        );
    }

    /**
     * Creates a new exception for when a cache file does not exist.
     *
     * @param string $file The path to the cache file.
     *
     * @return CacheException The new exception.
     */
    public static function fileNotExist($file)
    {
        return new self(
            "The cache file \"$file\" does not exist."
        );
    }

    /**
     * Creates an exception for when trying to dump a non-builder container.
     *
     * @return CacheException The new exception.
     */
    public static function notBuilder()
    {
        return new self(
            'The container could not be cached because it is not a ContainerBuilder.'
        );
    }
}
