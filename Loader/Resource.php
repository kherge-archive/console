<?php

namespace Box\Component\Console\Loader;

/**
 * Manages information about a specific resource.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Resource
{
    /**
     * The resource.
     *
     * @var mixed
     */
    protected $resource;

    /**
     * The type of the resource.
     *
     * @var string
     */
    protected $type;

    /**
     * Sets the resource and type.
     *
     * @param mixed  $resource The resource.
     * @param string $type     The type of the resource.
     */
    public function __construct($resource, $type = null)
    {
        $this->resource = $resource;
        $this->type = $type;
    }

    /**
     * Returns the resource.
     *
     * @return mixed The resource.
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Returns the type of the resource.
     *
     * @return string The type of the resource.
     */
    public function getType()
    {
        return $this->type;
    }
}
