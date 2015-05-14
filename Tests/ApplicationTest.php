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
            $this->container->get(Application::SERVICE_ID . '.helper_set'),
            $this->container->get(Application::SERVICE_ID)->getHelperSet()
        );

        // make sure the default helpers are registered
        self::assertNotNull(
            $this
                ->container
                ->has(Application::SERVICE_ID . '.helper.formatter')
        );
    }
}
