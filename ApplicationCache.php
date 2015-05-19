<?php

namespace Box\Component\Console;

use Box\Component\Console\Exception\CacheException;
use KHerGe\File\File;
use Symfony\Component\Config\ConfigCache;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Dumper\PhpDumper;
use Symfony\Component\DependencyInjection\Dumper\XmlDumper;

/**
 * Manages the loading and saving of the application container cache.
 *
 * **Note:** The caching methods each accept a `$debug` argument. The argument
 * is set to true by default. "Debugging" allows the cache manager to check if
 * resources have changed since the container was last built. This includes
 * resources such as configuration files and compiler pass classes. If these
 * resources have changed and debugging is enabled, the cache will be flagged
 * as stale. With debugging disabled, the cache will always be flagged as
 * fresh, event when the resources have changed.
 *
 * While it is recommended that debugging always be enabled, you may disable
 * debugging if you are certain that your resources will never change. If they
 * do, you will need to manually delete the cache files.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ApplicationCache extends Application
{
    /**
     * Loads a cached application or creates a new one.
     *
     * @param string   $file  The path to the cache file.
     * @param callable $setup The callback that sets up a new container.
     * @param string   $class The container class name.
     * @param boolean  $debug Enable debugging? (see class doc)
     *
     * @return ApplicationCache The application.
     */
    public static function bootstrap(
        $file,
        callable $setup = null,
        $class = 'ConsoleContainer',
        $debug = true
    ) {
        try {
            $app = self::load($file, $class, $debug);
        } catch (CacheException $exception) {
            $container = new ContainerBuilder();
            $app = new static($container);

            if (null !== $setup) {
                $setup($container);
            }

            $app->save($file, $class, $debug);
        }

        return $app;
    }

    /**
     * Loads an application using its cached container.
     *
     * @param string  $file  The path to the cache file.
     * @param string  $class The container class name.
     * @param boolean $debug Enable debugging? (see class doc)
     *
     * @return ApplicationCache The loaded application.
     *
     * @throws CacheException If the application could not be loaded.
     */
    public static function load(
        $file,
        $class = 'ConsoleContainer',
        $debug = true
    ) {
        if (!file_exists($file)) {
            throw CacheException::fileNotExist($file); // @codeCoverageIgnore
        }

        $cache = new ConfigCache($file, $debug);

        if (!$cache->isFresh()) {
            throw CacheException::cacheStale($file); // @codeCoverageIgnore
        }

        require_once $file;

        if (!class_exists($class)) {
            throw CacheException::classNotExist($class, $file); // @codeCoverageIgnore
        }

        return new static(new $class());
    }

    /**
     * Saves the application container to a cache file
     *
     * @param string  $file  The path to the cache file.
     * @param string  $class The container class name.
     * @param boolean $debug Enable debugging? (see class doc)
     *
     * @throws CacheException If the application could not be saved.
     */
    public function save($file, $class = 'ConsoleContainer', $debug = true)
    {
        $container = $this->getContainer();

        if (!($container instanceof ContainerBuilder)) {
            throw CacheException::notBuilder(); // @codeCoverageIgnore
        }

        $this->dumpXmlConfig($container, $file);

        if (!$container->isFrozen()) {
            $container->compile();
        }

        $dumper = new PhpDumper($container);
        $cache = new ConfigCache($file, $debug);

        $cache->write(
            $dumper->dump(
                array(
                    'class' => $class
                )
            ),
            $container->getResources()
        );
    }

    /**
     * Dumps the container configuration as an XML file for rebuilding.
     *
     * @param ContainerBuilder $container The container builder.
     * @param string           $file      The path to the cache file.
     */
    private function dumpXmlConfig(ContainerBuilder $container, $file)
    {
        $file = sprintf(
            '%s%s%s.xml',
            dirname($file),
            DIRECTORY_SEPARATOR,
            pathinfo($file, PATHINFO_FILENAME)
        );

        $container
            ->getDefinition(self::getId('helper.container'))
            ->addMethodCall(
                'setFile',
                array($file)
            )
        ;

        $dumper = new XmlDumper($container);
        $writer = new File($file, 'w');

        $writer->fwrite($dumper->dump());
    }
}
