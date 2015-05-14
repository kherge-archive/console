<?php

namespace Box\Component\Console\Tests\Helper;

use Box\Component\Console\Helper\ContainerHelper;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @covers \Box\Component\Console\Helper\ContainerHelper
 */
class ContainerHelperTest extends TestCase
{
    /**
     * The test container.
     *
     * @var Container
     */
    private $container;

    /**
     * The helper instance being tested.
     *
     * @var ContainerHelper
     */
    private $helper;

    /**
     * Verifies that we can retrieve the container.
     */
    public function testGetContainer()
    {
        self::assertSame($this->container, $this->helper->getContainer());
    }

    /**
     * Verifies that we can retrieve the name.
     */
    public function testGetName()
    {
        self::assertEquals('container', $this->helper->getName());
    }

    /**
     * Creates a new test container and helper instance.
     */
    protected function setUp()
    {
        $this->container = new Container();

        $this->helper = new ContainerHelper($this->container);
    }
}
