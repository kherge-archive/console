<?php

namespace Box\Component\Console\DependencyInjection;

use Herrera\Util\ObjectStorage;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Manages a collection of container extensions.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ExtensionCollection extends ObjectStorage
{
    /**
     * {@inheritdoc}
     *
     * @return ExtensionCollection For method chaining.
     */
    public function attach($object, $data = null)
    {
        parent::attach($object, $data);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isSupported($extension)
    {
        return ($extension instanceof ExtensionInterface);
    }
}
