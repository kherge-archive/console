<?php

namespace Box\Component\Console\Tests;

use Box\Component\Console\Application;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ApplicationTest extends TestCase
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
}
