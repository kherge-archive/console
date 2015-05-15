<?php

namespace Box\Component\Console\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension as Base;

/**
 * A container extension for the test.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Extension extends Base
{
    /**
     * {@inheritdoc}
     */
    public function getAlias()
    {
        return 'test';
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        /** @var Configuration $configuration */
        $configuration = $this->getConfiguration($configs, $container);

        $container->setParameter(
            $configuration->getParameterName(),
            $this->processConfiguration(
                $configuration,
                $configs
            )
        );
    }
}
