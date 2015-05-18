<?php

namespace Box\Component\Console\Tests\Loader;

use Box\Component\Console\Loader\Resource;
use Box\Component\Console\Loader\ResourceCollection;
use Box\Component\Console\Loader\ResourceCollectionLoader;
use Box\Component\Console\Loader\ResourceSupport;
use KHerGe\File\Utility;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ResourceCollectionLoaderTest extends TestCase
{
    /**
     * The test container instance.
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
     * @var ResourceCollectionLoader
     */
    private $loader;

    /**
     * The test loader resolver instance.
     *
     * @var LoaderResolver
     */
    private $resolver;

    /**
     * Verifies that we can load a resource.
     */
    public function testLoad()
    {
        file_put_contents(
            $this->dir . '/test.yml.dist',
            Yaml::dump(
                array(
                    'parameters' => array(
                        'test' => 'value'
                    )
                )
            )
        );

        $this->loader->load(
            new ResourceCollection(
                array(
                    new ResourceSupport('test.yml.dist', 'test.yml')
                )
            )
        );

        self::assertEquals('value', $this->container->getParameter('test'));
    }

    /**
     * Verifies that we can load an optional resource.
     */
    public function testLoadOptional()
    {
        file_put_contents(
            $this->dir . '/test.yml.dist',
            Yaml::dump(
                array(
                    'parameters' => array(
                        'test' => 'value'
                    )
                )
            )
        );

        self::assertFalse(
            $this->loader->loadOptional(
                new ResourceCollection(
                    array(
                        new Resource('test.yml')
                    )
                )
            )
        );

        self::assertTrue(
            $this->loader->loadOptional(
                new ResourceCollection(
                    array(
                        new ResourceSupport('test.yml.dist', 'test.yml')
                    )
                )
            )
        );

        self::assertEquals('value', $this->container->getParameter('test'));
    }

    /**
     * Verifies that an invalid resource throws an exception.
     */
    public function testLoadInvalid()
    {
        file_put_contents($this->dir . '/test.yml', ':');

        $this->setExpectedException(
            'InvalidArgumentException',
            "The service file \"$this->dir/test.yml\" is not valid."
        );

        $this->loader->load(
            new ResourceCollection(
                array(
                    new Resource('test.yml')
                )
            )
        );
    }

    /**
     * Verifies that an exception is thrown if no resources are loaded.
     */
    public function testLoadUnsupported()
    {
        $this->setExpectedException(
            'Symfony\Component\Config\Exception\FileLoaderLoadException',
            'Cannot load resource'
        );

        $this->loader->load(
            new ResourceCollection(
                array(
                    new Resource('a.yml'),
                    new Resource('b.yml')
                )
            )
        );
    }

    /**
     * Verifies that we can test for support.
     */
    public function testSupports()
    {
        $collection = new ResourceCollection();

        self::assertFalse($this->loader->supports('test.yml'));
        self::assertFalse($this->loader->supports($collection));

        $collection->attach(new Resource('test.yml'));

        self::assertTrue($this->loader->supports($collection));
    }

    /**
     * Sets up the loader for testing.
     */
    protected function setUp()
    {
        $this->dir = tempnam(sys_get_temp_dir(), 'box-');

        unlink($this->dir);
        mkdir($this->dir);

        $this->container = new ContainerBuilder();
        $this->resolver = new LoaderResolver(
            array(
                new YamlFileLoader(
                    $this->container,
                    new FileLocator($this->dir)
                )
            )
        );

        $this->loader = new ResourceCollectionLoader($this->resolver);
    }

    /**
     * Destroys the temporary configuration directory.
     */
    protected function tearDown()
    {
        if (file_exists($this->dir)) {
            Utility::remove($this->dir);
        }
    }
}
