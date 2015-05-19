<?php

namespace Box\Component\Console\Tests;

use Box\Component\Console\ApplicationCache;
use KHerGe\File\Utility;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\Yaml\Yaml;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ApplicationCacheTest extends TestCase
{
    /**
     * The test configuration directory.
     *
     * @var string
     */
    private $dir;

    /**
     * Verifies that we can create and reload a cached container.
     *
     * @covers \Box\Component\Console\ApplicationCache
     *
     * @runInSeparateProcess
     */
    public function testCache()
    {
        // create the test configuration file
        $file = $this->dir . '/test.yml';

        file_put_contents(
            $file,
            Yaml::dump(
                array(
                    'parameters' => array(
                        'box.console.name' => 'test name',
                        'box.console.version' => 'test version'
                    )
                )
            )
        );

        $cache = $this->dir . '/new/cache.php';

        // bootstrap the application
        $app = ApplicationCache::bootstrap(
            $cache,
            function (ContainerBuilder $container) use ($file) {
                $loader = new YamlFileLoader($container, new FileLocator());
                $loader->load($file);
            }
        );

        // make sure a new container was created
        self::assertInstanceOf(
            'Symfony\Component\DependencyInjection\ContainerBuilder',
            $app->getContainer()
        );

        // make sure the configuration file was loaded
        $console = $app->getContainer()->get(ApplicationCache::getId());

        self::assertEquals('test name', $console->getName());
        self::assertEquals('test version', $console->getVersion());

        // make sure the cache files were created
        self::assertFileExists($this->dir . '/new/cache.php');
        self::assertFileExists($this->dir . '/new/cache.php.meta');

        // change the cache file times so it isn't immediately treated as stale
        touch(
            $this->dir . '/new/cache.php',
            filemtime($this->dir . '/new/cache.php') + 1
        );

        touch(
            $this->dir . '/new/cache.php.meta',
            filemtime($this->dir . '/new/cache.php.meta') + 1
        );

        // bootstrap the application again
        $app = ApplicationCache::bootstrap(
            $cache,
            function (ContainerBuilder $container) use ($file) {
                $loader = new YamlFileLoader($container, new FileLocator());
                $loader->load($file);
            }
        );

        // make sure that the cached container is used
        self::assertFalse($app->getContainer() instanceof ContainerBuilder);
    }

    /**
     * Creates a new test configuration file.
     */
    protected function setUp()
    {
        $this->dir = tempnam(sys_get_temp_dir(), 'box-');

        unlink($this->dir);
        mkdir($this->dir);
    }

    /**
     * Deletes the test configuration file.
     */
    protected function tearDown()
    {
        if (file_exists($this->dir)) {
            Utility::remove($this->dir);
        }
    }
}
