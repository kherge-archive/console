<?php

namespace Box\Component\Console;

use Box\Component\Console\DependencyInjection\Compiler\CommandPass;
use Box\Component\Console\DependencyInjection\Compiler\HelperPass;
use Box\Component\Console\Exception\DefinitionException;
use ReflectionMethod;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\EventDispatcher\DependencyInjection\RegisterListenersPass;

/**
 * Basis for a console application assembled by dependency injection.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Application
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
     * Returns the application service identifier.
     *
     * @param string $id The identifier to append.
     *
     * @return string The service identifier.
     */
    public static function getId($id = null)
    {
        if (null === $id) {
            return 'box.console';
        }

        return 'box.console.' . $id;
    }

    /**
     * Runs the console application.
     *
     * @param InputInterface  $input  The input manager.
     * @param OutputInterface $output The output manager.
     *
     * @return integer The exit status.
     */
    public function run(
        InputInterface $input = null,
        OutputInterface $output = null
    ) {
        if (null === $input) {
            $input = $this
                ->getContainer()
                ->get(self::getId('input'))
            ;
        }

        if (null === $output) {
            $output = $this
                ->getContainer()
                ->get(self::getId('output'))
            ;
        }

        return $this
            ->container
            ->get(self::getId())
            ->run($input, $output)
        ;
    }

    /**
     * Returns the list of compiler passes.
     *
     * @return CompilerPassInterface[] The compiler passes.
     */
    protected function getCompilerPasses()
    {
        $passes = array(
            PassConfig::TYPE_BEFORE_OPTIMIZATION => array(

                // box.console.command
                new CommandPass(
                    self::getId('command'),
                    'Symfony\Component\Console\Command\Command'
                ),

                // box.console.helper
                new HelperPass(
                    self::getId('helper'),
                    'Symfony\Component\Console\Helper\Helper'
                )

            )
        );

        /*
         * box.console.event.listener
         * box.console.event.subscriber
         */
        if (class_exists('Symfony\Component\EventDispatcher\EventDispatcher')) {
            $passes[] = new RegisterListenersPass(
                self::getId('event_dispatcher'),
                self::getId('event.listener'),
                self::getId('event.subscriber')
            );
        }

        return $passes;
    }

    /**
     * Returns the default list of commands.
     *
     * The list of commands is retrieved from an instance of the application
     * class that is instantiated without its constructor. The key is the name
     * of the command (with ":" replace with "_"), while the value is the fully
     * qualified name of the command class.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return array The list of command classes.
     */
    protected function getDefaultCommands(ContainerBuilder $container)
    {
        $reflection = new ReflectionMethod(
            $container->getParameter(self::getId('class')),
            'getDefaultCommands'
        );

        $reflection->setAccessible(true);

        $set = $reflection->invoke(
            $reflection
                ->getDeclaringClass()
                ->newInstanceWithoutConstructor()
        );

        $commands = array();

        /** @var Command $command */
        foreach ($set as $command) {
            $name = str_replace(':', '_', $command->getName());

            $commands[$name] = get_class($command);
        }

        if (class_exists('Symfony\Bundle\FrameworkBundle\Command\ContainerDebugCommand')) {
            $commands['debug_container'] = 'Box\Component\Console\Command\Debug\ContainerCommand';
        }

        return $commands;
    }

    /**
     * Returns the default list of helpers.
     *
     * The list of helpers is retrieved from an instance of the application
     * class that is instantiated without its constructor. The key is the name
     * of the helper, while the value is the fully qualified name of the helper
     * class.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return array The list of helper classes.
     */
    protected function getDefaultHelpers(ContainerBuilder $container)
    {
        $reflection = new ReflectionMethod(
            $container->getParameter(self::getId('class')),
            'getDefaultHelperSet'
        );

        $reflection->setAccessible(true);

        $set = $reflection->invoke(
            $reflection
                ->getDeclaringClass()
                ->newInstanceWithoutConstructor()
        );

        $helpers = array();

        foreach ($set as $name => $helper) {
            $helpers[$name] = get_class($helper);
        }

        return $helpers;
    }

    /**
     * Adds a method call to a definition if one is not already made.
     *
     * The `$id` is prefixed with `SERVICE_ID`.
     *
     * @param ContainerBuilder $container  The container.
     * @param string           $id         The service identifier.
     * @param string           $method     The name of the method.
     * @param array            $arguments  The call arguments.
     *
     * @return Application For method chaining.
     *
     * @throws DefinitionException If the definition does not exist.
     */
    private function addMethodCall(
        ContainerBuilder $container,
        $id,
        $method,
        array $arguments = array()
    ) {
        $id = self::getId($id);

        if (!$container->hasDefinition($id)) {
            throw DefinitionException::notExist($id); // @codeCoverageIgnore
        }

        $definition = $container->getDefinition($id);

        foreach ($definition->getMethodCalls() as $call) {
            if ($method === $call[0]) {
                return $this; // @codeCoverageIgnore
            }
        }

        $definition->addMethodCall($method, $arguments);

        return $this;
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
            ->registerEventDispatcher($container)
            ->registerCompilerPasses($container)
            ->registerHelperSet($container)
            ->registerDefaultHelpers($container)
            ->registerContainerHelper($container)
            ->registerDefaultCommands($container)
            ->registerInputManager($container)
            ->registerOutputManager($container)
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
                null,
                function () {
                    $definition = new Definition(
                        '%' . self::getId('class') . '%'
                    );

                    $definition->addArgument('%' . self::getId('name') . '%');
                    $definition->addArgument('%' . self::getId('version') . '%');

                    $definition->addMethodCall(
                        'setAutoExit',
                        array(
                            '%' . self::getId('auto_exit') . '%'
                        )
                    );

                    return $definition;
                }
            )

            // box.console.auto_exit
            ->setParameter($container, 'auto_exit', false)

            // box.console.class
            ->setParameter(
                $container,
                'class',
                'Symfony\Component\Console\Application'
            )

            // box.console.name
            ->setParameter($container, 'name', 'UNKNOWN')

            // box.console.version
            ->setParameter($container, 'version', 'UNKNOWN')

        ;

        return $this;
    }

    /**
     * Registers the default list of compiler passes.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerCompilerPasses(ContainerBuilder $container)
    {
        foreach ($this->getCompilerPasses() as $type => $passes) {
            foreach ($passes as $pass) {
                $container->addCompilerPass($pass, $type);
            }
        }

        return $this;
    }

    /**
     * Registers the container as a helper.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerContainerHelper(ContainerBuilder $container)
    {
        $this

            // box.console.helper.container
            ->setDefinition(
                $container,
                'helper.container',
                function () {
                    $definition = new Definition(
                        '%' . self::getId('helper.container.class') . '%'
                    );

                    $definition->addArgument(new Reference('service_container'));
                    $definition->addTag(self::getId('helper'));

                    return $definition;
                }
            )

            // box.console.helper.container.class
            ->setParameter(
                $container,
                'helper.container.class',
                'Box\Component\Console\Helper\ContainerHelper'
            )

        ;

        return $this;
    }

    /**
     * Registers the default list of commands.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerDefaultCommands(ContainerBuilder $container)
    {
        $commands = $this->getDefaultCommands($container);

        foreach ($commands as $name => $class) {
            $this

                // box.console.command.?
                ->setDefinition(
                    $container,
                    "command.$name",
                    function () use ($name) {
                        $definition = new Definition(
                            '%' . self::getId("command.$name.class") . '%'
                        );

                        $definition->addTag(self::getId('command'));

                        return $definition;
                    }
                )

                // box.console.command.?.class
                ->setParameter($container, "command.$name.class", $class)

            ;
        }

        return $this;
    }

    /**
     * Registers the default list of helpers.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerDefaultHelpers(ContainerBuilder $container)
    {
        $helpers = $this->getDefaultHelpers($container);

        foreach ($helpers as $name => $class) {
            $this

                // box.console.helper.?
                ->setDefinition(
                    $container,
                    "helper.$name",
                    function () use ($name) {
                        $definition = new Definition(
                            '%' . self::getId("helper.$name.class") . '%'
                        );

                        $definition->addTag(self::getId('helper'));

                        return $definition;
                    }
                )

                // box.console.helper.?.class
                ->setParameter($container, "helper.$name.class", $class)

            ;
        }

        return $this;
    }

    /**
     * Registers the event dispatcher with the container.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerEventDispatcher(ContainerBuilder $container)
    {
        if (!class_exists('Symfony\Component\EventDispatcher\EventDispatcher')) {
            return $this; // @codeCoverageIgnore
        }

        $this

            // box.console.event_dispatcher
            ->setDefinition(
                $container,
                'event_dispatcher',
                function () {
                    $definition = new Definition(
                        '%' . self::getId('event_dispatcher.class') . '%'
                    );

                    $definition->addArgument(
                        new Reference('service_container')
                    );

                    return $definition;
                }
            )

            ->addMethodCall(
                $container,
                null,
                'setDispatcher',
                array(
                    new Reference(self::getId('event_dispatcher'))
                )
            )

            // box.console.event_dispatcher.class
            ->setParameter(
                $container,
                'event_dispatcher.class',
                'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher'
            )

        ;

        return $this;
    }

    /**
     * Registers the helper set with the container.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerHelperSet(ContainerBuilder $container)
    {
        $this

            // box.console.helper_set
            ->setDefinition(
                $container,
                'helper_set',
                function () {
                    return new Definition(
                        '%' . self::getId('helper_set.class') . '%'
                    );
                }
            )

            // Application->setHelperSet()
            ->addMethodCall(
                $container,
                null,
                'setHelperSet',
                array(
                    new Reference(self::getId('helper_set'))
                )
            )

            // box.console.helper_set.class
            ->setParameter(
                $container,
                'helper_set.class',
                'Symfony\Component\Console\Helper\HelperSet'
            )

        ;

        return $this;
    }

    /**
     * Registers the input manager with the container.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerInputManager(ContainerBuilder $container)
    {
        $this

            // box.console.input
            ->setDefinition(
                $container,
                'input',
                function () {
                    return new Definition(
                        '%' . self::getId('input.class') . '%'
                    );
                }
            )

            // box.console.input.class
            ->setParameter(
                $container,
                'input.class',
                'Symfony\Component\Console\Input\ArgvInput'
            )

        ;

        return $this;
    }

    /**
     * Registers the output manager with the container.
     *
     * @param ContainerBuilder $container The container.
     *
     * @return Application For method chaining.
     */
    private function registerOutputManager(ContainerBuilder $container)
    {
        $this

            // box.console.output
            ->setDefinition(
                $container,
                'output',
                function () {
                    return new Definition(
                        '%' . self::getId('output.class') . '%'
                    );
                }
            )

            // box.console.output.class
            ->setParameter(
                $container,
                'output.class',
                'Symfony\Component\Console\Output\ConsoleOutput'
            )

        ;

        return $this;
    }

    /**
     * Sets the default definition if it is not already set.
     *
     * The `$id` is prefixed with `SERVICE_ID`.
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
        $id = self::getId($id);

        if (!$container->hasDefinition($id)) {
            $container->setDefinition($id, $definition($container));
        }

        return $this;
    }

    /**
     * Sets the default parameter value if it is not already set.
     *
     * The `$name` is prefixed with `SERVICE_ID`.
     *
     * @param ContainerBuilder $container The container.
     * @param string           $name      The name of the parameter.
     * @param mixed            $value     The value of the parameter.
     *
     * @return Application For method chaining.
     */
    private function setParameter(ContainerBuilder $container, $name, $value)
    {
        $name = self::getId($name);

        if (!$container->hasParameter($name)) {
            $container->setParameter($name, $value);
        }

        return $this;
    }
}
