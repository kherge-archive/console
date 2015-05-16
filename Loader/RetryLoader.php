<?php

namespace Box\Component\Console\Loader;

use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\Loader\LoaderResolver;

/**
 * Performs multiple attempts to load the same resource.
 *
 * The loader is similar to the `DelegatingLoader` provided by the `Config`
 * component. The `RetryLoader` loader accepts modifiers that will alter the
 * resource and type when finding a supported loader.  If you have a suffix
 * incompatible with an otherwise compatible loader, using a modifier will
 * allow you to make another attempt at loading the file.
 *
 * This example will allow you to load `.xml.dist` files using the `.xml`
 * file loader, `XmlFileLoader`.
 *
 * ```php
 * use Box\Component\Console\Loader\RetryLoader;
 * use Symfony\Component\Config\FileLocator;
 * use Symfony\Component\Config\Loader\LoaderResolver;
 * use Symfony\Component\DependencyInjection\ContainerBuilder;
 * use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
 *
 * $container = new ContainerBuilder();
 * $locator = new FileLocator(array('.'));
 * $loader = new RetryLoader(
 *     new LoaderResolver(
 *         array(
 *             new XmlFileLoader($container, $locator)
 *         )
 *     )
 * );
 *
 * $loader->addModifier(
 *     function ($resource, $type) {
 *         $resource = explode('.dist', $resource);
 *
 *         return array(array_shift($resource), $type);
 *     }
 * );
 *
 * $loader->load('example.xml.dist');
 * ```
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class RetryLoader extends Loader
{
    /**
     * The list of support modifiers.
     *
     * @var callable[]
     */
    private $modifiers = array();

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolver $resolver The loader resolver.
     */
    public function __construct(LoaderResolver $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * Adds a support modifier.
     *
     * @param callable $modifier The support modifier.
     *
     * @return RetryLoader For method chaining.
     */
    public function addModifier(callable $modifier)
    {
        $this->modifiers[] = $modifier;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if (null === ($loader = $this->resolve($resource, $type))) {
            throw new FileLoaderLoadException($resource); // @codeCoverageIgnore
        }

        return $loader->load($resource, $type);
    }

    /**
     * Returns the loader for the given resource and resource type.
     *
     * @param mixed       $resource The resource to be loaded.
     * @param null|string $type     The type of the resource.
     *
     * @return Loader The resolved loader.
     */
    public function resolve($resource, $type = null)
    {
        $loader = $this->resolver->resolve($resource, $type);

        if (false === $loader) {
            foreach ($this->modifiers as $modifier) {
                list($r, $t) = $modifier($resource, $type);

                $loader = $this->resolver->resolve($r, $t);

                if (false !== $loader) {
                    break;
                }
            }
        }

        return (false === $loader) ? null : $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return (null !== $this->resolve($resource, $type));
    }
}
