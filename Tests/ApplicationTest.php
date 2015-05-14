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
 * @covers \Box\Component\Console\Test\CommandTestCase
 */
class ApplicationTest extends CommandTestCase
{
    /**
     * Verifies that we can set and retrieve the container.
     *
     * @covers \Box\Component\Console\Application::__construct
     * @covers \Box\Component\Console\Application::getContainer
     */
    public function testContainer()
    {
        $container = new Container();

        $app = new Application($container);

        self::assertSame($container, $app->getContainer());
    }

    /**
     * Verifies that we can run the application.
     *
     * @covers \Box\Component\Console\Application::run
     */
    public function testRun()
    {
        self::assertEquals(
            0,
            $this->runCommand(
                new ArrayInput(array()),
                $output
            )
        );

        self::assertContains(
            'help',
            $this->readOutput($output)
        );
    }
}
