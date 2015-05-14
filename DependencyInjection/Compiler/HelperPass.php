<?php

namespace Box\Component\Console\DependencyInjection\Compiler;

use Box\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers the helpers with the helper set.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class HelperPass extends AbstractTaggedPass
{
    /**
     * {@inheritdoc}
     */
    protected function register(
        ContainerBuilder $container,
        Definition $definition,
        $id,
        array $tag
    ) {
        $container
            ->getDefinition(Application::getId('helper_set'))
            ->addMethodCall(
                'set',
                array(
                    new Reference($id)
                )
            )
        ;
    }
}
