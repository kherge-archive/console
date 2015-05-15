<?php

namespace Box\Component\Console\Helper;

use Box\Component\Console\Exception\CacheException;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

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
     * The configuration file path.
     *
     * @var string
     */
    private $file;

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
     * Returns the container builder.
     *
     * @return ContainerBuilder The container builder.
     *
     * @throws CacheException If the cached configuration file does not exist.
     */
    public function getContainerBuilder()
    {
        if (null === $this->file) {
            throw CacheException::fileNotExist($this->file); // @codeCoverageIgnore
        }

        $container = new ContainerBuilder();

        $loader = new XmlFileLoader($container, new FileLocator());
        $loader->load($this->file);

        return $container;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'container';
    }

    /**
     * Sets the configuration file path.
     *
     * @param string $file The file path.
     */
    public function setFile($file)
    {
        $this->file = $file;
    }
}
