<?php

namespace Box\Component\Console\Tests\DependencyInjection;

use Box\Component\Console\DependencyInjection\Configuration\DefaultsInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * A configuration for the test.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Configuration implements ConfigurationInterface, DefaultsInterface
{
    /**
     * The default values.
     *
     * @var array
     */
    private $defaults = array(
        'alpha' => 'A',
        'beta' => 2,
        'gamma' => true
    );

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder();
        $root = $tree->root('test');

        /** @noinspection PhpUndefinedMethodInspection */
        $root
            ->children()
                ->scalarNode('alpha')
                    ->info('The alpha setting.')
                    ->defaultValue($this->defaults['alpha'])
                ->end()
                ->scalarNode('beta')
                    ->info('The beta setting.')
                    ->defaultValue($this->defaults['beta'])
                ->end()
                ->scalarNode('gamma')
                    ->info('The gamma setting.')
                    ->defaultValue($this->defaults['gamma'])
                ->end()
        ;

        return $tree;
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValues()
    {
        return $this->defaults;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameterName()
    {
        return 'current.settings';
    }

    /**
     * {@inheritdoc}
     */
    public function mergeDefaultValues(array $values)
    {
        $this->defaults = array_merge($this->defaults, $values);
    }
}
