<?php

namespace Box\Component\Console\Tests;

use Box\Component\Console\Application;
use Box\Component\Console\Test\CommandTestCase;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\DependencyInjection\Container;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @covers \Box\Component\Console\Application
 * @covers \Box\Component\Console\DependencyInjection\Compiler\AbstractTaggedPass
 * @covers \Box\Component\Console\DependencyInjection\Compiler\CommandPass
 * @covers \Box\Component\Console\DependencyInjection\Compiler\HelperPass
 * @covers \Box\Component\Console\Test\CommandTestCase
 */
class ApplicationTest extends CommandTestCase
{
    /**
     * Verifies that we can set and retrieve the container.
     */
    public function testContainer()
    {
        $container = new Container();

        $app = new Application($container);

        self::assertSame($container, $app->getContainer());
    }

    /**
     * Verifies that we can run the application.
     */
    public function testRun()
    {
        // make sure it does not exit
        self::assertEquals(
            0,
            $this->runCommand(
                new ArrayInput(array()),
                $output
            )
        );

        // make sure it uses our output
        self::assertContains(
            'help',
            $this->readOutput($output)
        );

        // make sure the helper set is registered
        self::assertSame(
            $this->container->get(Application::getId('helper_set')),
            $this->container->get(Application::getId())->getHelperSet()
        );

        // make sure the default helpers are registered
        self::assertNotNull(
            $this
                ->container
                ->has(Application::getId('helper.formatter'))
        );

        // make sure the helpers are registered with the helper set
        $helperSet = $this->container->get(Application::getId('helper_set'));

        self::assertSame(
            $this
                ->container
                ->get(Application::getId('helper.formatter')),
            $helperSet->get('formatter')
        );

        // make sure that the container helper is registered
        self::assertSame(
            $this->container,
            $helperSet->get('container')->getContainer()
        );

        // make sure the default commands are registered
        self::assertNotNull(
            $this
                ->container
                ->has(Application::getId('command.help'))
        );

        // make sure the commands are registered with the application.
        $command = $this
            ->container
            ->get(Application::getId('command.help'))
        ;

        self::assertSame(
            $command,
            $this
                ->container
                ->get(Application::getId())
                ->find($command->getName())
        );

        // make sure the commands use the helper set service
        self::assertSame(
            $helperSet,
            $command->getHelperSet()
        );
    }
}
