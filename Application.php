<?php

namespace Box\Component\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Basis for a console application assembled by dependency injection.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Application
{
    /**
     * The application service identifier.
     *
     * @var string
     */
    const SERVICE_ID = 'box.console';

    /**
     * The container.
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * If an instance of `ContainerBuilder` is provided, the default parameters
     * and services will be registered with the container. If a parameter or a
     * service definition with the same identifier or key exists, the default
     * will not be set for that specific parameter or service definition.
     *
     * @param ContainerInterface $container The container.
     */
    public function __construct(ContainerInterface $container)
    {
        if ($container instanceof ContainerBuilder) {
            $this->prepareContainer($container);
        }

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
     * Runs the console application.
     *
     * @param InputInterface  $input  The input manager.
     * @param OutputInterface $output The output manager.
     *
     * @return integer The exit status.
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        return $this
            ->container
            ->get(self::SERVICE_ID)
            ->run($input, $output)
        ;
    }

    /**
     * Registers the component services and compilers with the container.
     *
     * @param ContainerBuilder $container The container.
     */
    private function prepareContainer(ContainerBuilder $container)
    {
        $this
            ->registerApplication($container)
        ;
    }

    /**
     * Registers the console application with the container.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerApplication(ContainerBuilder $container)
    {
        $this

            // box.console
            ->setDefinition(
                $container,
                self::SERVICE_ID,
                function () {
                    $definition = new Definition(
                        '%' . self::SERVICE_ID . '.class%'
                    );

                    $definition->addArgument(self::SERVICE_ID . '.name');
                    $definition->addArgument(self::SERVICE_ID . '.version');

                    $definition->addMethodCall(
                        'setAutoExit',
                        array(
                            '%' . self::SERVICE_ID . '.auto_exit%'
                        )
                    );

                    return $definition;
                }
            )

            // box.console.auto_exit
            ->setParameter($container, self::SERVICE_ID . '.auto_exit', false)

            // box.console.class
            ->setParameter(
                $container,
                self::SERVICE_ID . '.class',
                'Symfony\Component\Console\Application'
            )

            // box.console.name
            ->setParameter($container, self::SERVICE_ID . '.name', 'UNKNOWN')

            // box.console.version
            ->setParameter($container, self::SERVICE_ID . '.version', 'UNKNOWN')

        ;
    }

    /**
     * Sets the default definition if it is not already set.
     *
     * @param ContainerBuilder $container  The container.
     * @param string           $id         The service identifier.
     * @param callable         $definition The definition builder.
     *
     * @return Application For method chaining.
     */
    private function setDefinition(
        ContainerBuilder $container,
        $id,
        callable $definition
    ) {
        if (!$container->hasDefinition($id)) {
            $container->setDefinition($id, $definition($container));
        }

        return $this;
    }

    /**
     * Sets the default parameter value if it is not already set.
     *
     * @param ContainerBuilder $container The container.
     * @param string           $name      The name of the parameter.
     * @param mixed            $value     The value of the parameter.
     *
     * @return Application For method chaining.
     */
    private function setParameter(ContainerBuilder $container, $name, $value)
    {
        if (!$container->hasParameter($name)) {
            $container->setParameter($name, $value);
        }

        return $this;
    }
}
