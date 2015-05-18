<?php

namespace Box\Component\Console\Loader;

use Herrera\Util\ObjectStorage;

/**
 * Manages a collection of `Resource` instances.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ResourceCollection extends ObjectStorage
{
    /**
     * Sets the resources for the collection.
     *
     * @param Resource[] $resources The resources.
     */
    public function __construct(array $resources = array())
    {
        foreach ($resources as $resource) {
            $this->attach($resource);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($object)
    {
        return ($object instanceof Resource);
    }
}
