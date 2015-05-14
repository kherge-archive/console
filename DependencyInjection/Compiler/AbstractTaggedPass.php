<?php

namespace Box\Component\Console\DependencyInjection\Compiler;

use Box\Component\Console\Exception\DefinitionException;
use ReflectionClass;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Manages the discovery and validation steps of registering tagged services.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
abstract class AbstractTaggedPass implements CompilerPassInterface
{
    /**
     * The required parent class.
     *
     * @var string
     */
    private $parent;

    /**
     * The tag name.
     *
     * @var string
     */
    private $tag;

    /**
     * Sets the tag name and parent class name.
     *
     * @param string $tag    The tag name.
     * @param string $parent The parent class name.
     */
    public function __construct($tag, $parent = null)
    {
        $this->parent = $parent;
        $this->tag = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $ids = $container->findTaggedServiceIds($this->tag);

        foreach ($ids as $id => $tag) {
            $definition = $container->getDefinition($id);

            $this->validate($container, $definition, $id, $tag);
            $this->register($container, $definition, $id, $tag);
        }
    }

    /**
     * Registers a tagged service with another service.
     *
     * @param ContainerBuilder $container  The container.
     * @param Definition       $definition The definition.
     * @param string           $id         The identifier.
     * @param array            $tag        The tag data.
     */
    abstract protected function register(
        ContainerBuilder $container,
        Definition $definition,
        $id,
        array $tag
    );

    /**
     * Checks if the definition is valid.
     *
     * @param ContainerBuilder $container  The container.
     * @param Definition       $definition The definition.
     * @param string           $id         The identifier.
     * @param array            $tag        The tag data.
     *
     * @throws DefinitionException If the definition is not valid.
     */
    protected function validate(
        ContainerBuilder $container,
        Definition $definition,
        $id,
        array $tag
    ) {
        if ($definition->isAbstract()) {
            throw DefinitionException::taggedServiceAbstract($id, $this->tag); // @codeCoverageIgnore
        }

        if (!$definition->isPublic()) {
            throw DefinitionException::taggedServiceNotPublic($id, $this->tag); // @codeCoverageIgnore
        }

        if (null !== $this->parent) {
            $reflection = new ReflectionClass(
                $container
                    ->getParameterBag()
                    ->resolveValue($definition->getClass())
            );

            // @codeCoverageIgnoreStart
            if (!$reflection->isSubclassOf($this->parent)) {
                throw DefinitionException::taggedServiceNotSubclass(
                    $id,
                    $this->tag,
                    $this->parent
                );
            }
            // @codeCoverageIgnoreEnd
        }
    }
}
