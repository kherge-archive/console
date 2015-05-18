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
