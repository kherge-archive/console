<?php

namespace Box\Component\Console\Tests\Loader;

use Box\Component\Console\Loader\RetryLoader;
use KHerGe\File\Utility;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @covers \Box\Component\Console\Loader\RetryLoader
 */
class RetryLoaderTest extends TestCase
{
    /**
     * The test container builder.
     *
     * @var ContainerBuilder
     */
    private $container;

    /**
     * The temporary configuration directory.
     *
     * @var string
     */
    private $dir;

    /**
     * The loader instance being tested.
     *
     * @var RetryLoader
     */
    private $loader;

    /**
     * Verifies that we can load a file.
     */
    public function testLoad()
    {
        file_put_contents(
            $this->dir . '/test.yml.dist',
            'parameters: { test: value }'
        );

        $this
            ->loader
            ->addModifier(
                function ($resource, $type) {
                    $resource = explode('.dist', $resource);

                    return array(array_shift($resource), $type);
                }
            )
            ->load('test.yml.dist')
        ;

        self::assertEquals(
            'value',
            $this->container->getParameter('test')
        );
    }

    /**
     * Verifies that we can check support for a file.
     */
    public function testSupports()
    {
        self::assertFalse($this->loader->supports('test.yml.dist'));

        $this
            ->loader
            ->addModifier(
                function ($resource, $type) {
                    $resource = explode('.dist', $resource);

                    return array(array_shift($resource), $type);
                }
            )
        ;

        self::assertTrue($this->loader->supports('test.yml.dist'));
    }

    /**
     * Creates a new temporary directory, container, and loader for testing.
     */
    protected function setUp()
    {
        $this->dir = tempnam(sys_get_temp_dir(), 'box-');

        unlink($this->dir);
        mkdir($this->dir);

        $this->container = new ContainerBuilder();
        $this->loader = new RetryLoader(
            new LoaderResolver(
                array(
                    new YamlFileLoader(
                        $this->container,
                        new FileLocator($this->dir)
                    )
                )
            )
        );
    }

    /**
     * Cleans up the temporary configuration directory.
     */
    protected function tearDown()
    {
        if (file_exists($this->dir)) {
            Utility::remove($this->dir);
        }
    }
}
