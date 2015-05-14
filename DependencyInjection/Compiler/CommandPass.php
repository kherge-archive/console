<?php

namespace Box\Component\Console\DependencyInjection\Compiler;

use Box\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Registers the commands with the application.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class CommandPass extends AbstractTaggedPass
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
        $definition->addMethodCall(
            'setHelperSet',
            array(
                new Reference(Application::getId('helper_set'))
            )
        );

        $container
            ->getDefinition(Application::getId(null))
            ->addMethodCall(
                'add',
                array(
                    new Reference($id)
                )
            )
        ;
    }
}
