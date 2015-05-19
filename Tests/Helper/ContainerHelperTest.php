<?php

namespace Box\Component\Console\Tests\Helper;

use Box\Component\Console\Helper\ContainerHelper;
use KHerGe\File\Utility;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

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
     * @var ContainerBuilder
     */
    private $container;

    /**
     * The temporary directory.
     *
     * @var string
     */
    private $dir;

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
     * Verifies that we can retrieve the container builder.
     */
    public function testGetContainerBuilder()
    {
        // create a test configuration file
        file_put_contents(
            $this->dir . '/test.xml',
            <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<container
  xmlns="http://symfony.com/schema/dic/services"
  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
  xsi:schemaLocation="http://symfony.com/schema/dic/services
                      http://symfony.com/schema/dic/services/services-1.0.xsd">

  <services>
    <service class="Box\Component\Console\Tests\DependencyInjection\Extension" id="test_extension">
      <tag name="box.console.extension"/>
    </service>
  </services>
</container>
XML
        );

        // set it as the configuration to use for the builder
        $this->helper->setFile($this->dir . '/test.xml');

        // test the new container builder
        $container = $this->helper->getContainerBuilder();

        self::assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerBuilder',
            $container
        );

        // make sure the extension was re-registered
        self::assertTrue($container->hasExtension('test'));
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
        $this->container = new ContainerBuilder();
        $this->dir = tempnam(sys_get_temp_dir(), 'box-');
        $this->helper = new ContainerHelper($this->container);

        unlink($this->dir);
        mkdir($this->dir);
    }

    /**
     * Destroys the temporary directory.
     */
    protected function tearDown()
    {
        if (file_exists($this->dir)) {
            Utility::remove($this->dir);
        }
    }
}
