<?php

namespace Box\Component\Console\Loader;

use InvalidArgumentException;
use Symfony\Component\Config\Exception\FileLoaderLoadException;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolverInterface;

/**
 * Loads a resource using another loader.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ResourceLoader implements LoaderInterface
{
    /**
     * The loader resolver.
     *
     * @var LoaderResolverInterface
     */
    private $resolver;

    /**
     * Sets the loader resolver.
     *
     * @param LoaderResolverInterface $resolver The loader resolver.
     */
    public function __construct(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function load($resource, $type = null)
    {
        if ($resource instanceof Resource) {
            if (null === ($loader = $this->resolve($resource))) {
                throw new FileLoaderLoadException($this->toString($resource)); // @codeCoverageIgnore
            }

            $loader->load($resource->getResource(), $resource->getType());
        } else {
            throw new FileLoaderLoadException($this->toString($resource)); // @codeCoverageIgnore
        }
    }

    /**
     * Attempts to load a resource that may not exist.
     *
     * If the `$resource` exists, it will be loaded and `true` is returned.
     * Otherwise, `false` is returned. An `InvalidArgumentException` exception
     * will only be thrown if the resource fails to load because it is invalid.
     *
     * @param Resource $resource The resource to load.
     *
     * @return boolean Returns `true` if loaded, `false` if not.
     *
     * @throws InvalidArgumentException If the resource is invalid.
     */
    public function loadOptional($resource)
    {
        // @codeCoverageIgnoreStart
        if (!($resource instanceof Resource)) {
            throw new InvalidArgumentException(
                'The resource "%s" is not an instance of "Resource".',
                $this->toString($resource)
            );
        }
        // @codeCoverageIgnoreEnd

        try {
            $this->load($resource);

            return true;
        } catch (InvalidArgumentException $exception) {
            if (false === strpos($exception->getMessage(), 'does not exist')) {
                throw $exception; // @codeCoverageIgnore
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getResolver()
    {
        return $this->resolver;
    }

    /**
     * Returns the loader for a resource.
     *
     * @param Resource $resource The resource.
     *
     * @return LoaderInterface|null The resource loader.
     */
    public function resolve(Resource $resource)
    {
        if ($resource instanceof ResourceSupport) {
            $loader = $this->resolver->resolve(
                $resource->getSupportResource(),
                $resource->getSupportType()
            );
        } else {
            $loader = $this->resolver->resolve(
                $resource->getResource(),
                $resource->getType()
            );
        }

        return (false === $loader) ? null : $loader;
    }

    /**
     * {@inheritdoc}
     */
    public function setResolver(LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, $type = null)
    {
        return (($resource instanceof Resource)
            && (null !== $this->resolve($resource)));
    }

    /**
     * Returns a string representation of the variable.
     *
     * @param mixed $variable The variable.
     *
     * @return string The string representation.
     *
     * @codeCoverageIgnore
     */
    protected function toString($variable)
    {
        if (is_object($variable)) {
            return sprintf('object(%s)', get_class($variable));
        } elseif (is_string($variable)) {
            return "\"$variable\"";
        }

        return gettype($variable);
    }
}
