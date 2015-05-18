<?php

namespace Box\Component\Console\Loader;

use InvalidArgumentException;
use Symfony\Component\Config\Exception\FileLoaderLoadException;

/**
 * Attempts to the next resource in a collection if the previous one failed.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ResourceCollectionLoader extends ResourceLoader
{
    /**
     * {@inheritdoc}
     */
    public function load($collection, $type = null)
    {
        if ($collection instanceof ResourceCollection) {
            foreach ($collection as $resource) {
                try {
                    parent::load($resource);

                    return;
                } catch (InvalidArgumentException $exception) {
                    if (false === strpos($exception->getMessage(), 'does not exist')) {
                        throw $exception;
                    }
                }
            }
        }

        throw new FileLoaderLoadException($this->toString($collection));
    }

    /**
     * Attempts to load a resource from a collection.
     *
     * If one of the resources exist, it will be loaded and `true` is returned.
     * Otherwise, `false` is returned.  An `InvalidArgumentException` exception
     * will only be thrown if the resource fails to load because it is invalid.
     *
     * @param ResourceCollection $collection The collection to load.
     *
     * @return boolean Returns `true` if loaded, `false` if not.
     *
     * @throws InvalidArgumentException If the resource is invalid..
     */
    public function loadOptional($collection)
    {
        // @codeCoverageIgnoreStart
        if (!($collection instanceof ResourceCollection)) {
            throw new InvalidArgumentException(
                'The resource "%s" is not an instance of "ResourceCollection".',
                $this->toString($collection)
            );
        }
        // @codeCoverageIgnoreEnd

        foreach ($collection as $resource) {
            try {
                parent::load($resource);

                return true;
            } catch (InvalidArgumentException $exception) {
                if (false === strpos($exception->getMessage(), 'does not exist')) {
                    throw $exception; // @codeCoverageIgnore
                }
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($collection, $type = null)
    {
        if ($collection instanceof ResourceCollection) {
            foreach ($collection as $resource) {
                if (parent::supports($resource)) {
                    return true;
                }
            }
        }

        return false;
    }
}
