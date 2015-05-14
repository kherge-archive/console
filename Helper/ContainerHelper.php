<?php

namespace Box\Component\Console\Helper;

use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides access to the container as a helper.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ContainerHelper extends Helper
{
    /**
     * The container.
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface $container The container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the container.
     *
     * @return ContainerInterface The container.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'container';
    }
}
