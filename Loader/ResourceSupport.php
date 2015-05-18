<?php

namespace Box\Component\Console\Loader;

/**
 * Manages alternative support information for a `Resource`.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ResourceSupport extends Resource
{
    /**
     * The support resource.
     *
     * @var mixed
     */
    protected $supportResource;

    /**
     * The type of the support resource.
     *
     * @var string
     */
    protected $supportType;

    /**
     * Sets the resource, resource type and support resource, resource type.
     *
     * @param mixed  $resource        The resource.
     * @param mixed  $supportResource The support resource.
     * @param string $type            The type of the resource.
     * @param string $supportType     The type of the support resource.
     */
    public function __construct(
        $resource,
        $supportResource,
        $type = null,
        $supportType = null
    ) {
        parent::__construct($resource, $type);

        $this->supportResource = $supportResource;
        $this->supportType = $supportType;
    }

    /**
     * Returns the support resource.
     *
     * @return mixed The support resource.
     */
    public function getSupportResource()
    {
        return $this->supportResource;
    }

    /**
     * Returns the type of the support resource.
     *
     * @return string The type of the support resource.
     */
    public function getSupportType()
    {
        return $this->supportType;
    }
}
