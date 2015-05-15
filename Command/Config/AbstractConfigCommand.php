<?php

namespace Box\Component\Console\Command\Config;

use Box\Component\Console\Command\AbstractCommand;
use Box\Component\Console\DependencyInjection\Configuration\DefaultsInterface;
use Box\Component\Console\Helper\ContainerHelper;
use InvalidArgumentException;
use LogicException;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Dumper\XmlReferenceDumper;
use Symfony\Component\Config\Definition\Dumper\YamlReferenceDumper;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * Manages shared functionality for configuration related commands.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
abstract class AbstractConfigCommand extends AbstractCommand
{
    /**
     * The container builder.
     *
     * @var ContainerBuilder
     */
    private $containerBuilder;

    /**
     * Sets the default configuration for a configuration command.
     */
    protected function configure()
    {
        $this
            ->addArgument(
                'alias',
                InputArgument::REQUIRED,
                'The alias for the extension'
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_REQUIRED,
                'The output format ("yaml" or "xml").',
                'yaml'
            )
        ;
    }

    /**
     * Returns the configuration for an extension.
     *
     * @param string  $alias   The alias for the extension.
     * @param boolean $current Return with current configuration?
     *
     * @return ConfigurationInterface The configuration.
     *
     * @throws LogicException If the configuration could not be returned.
     */
    protected function getConfiguration($alias, $current = false)
    {
        $configuration = $this
            ->getExtension($alias)
            ->getConfiguration(
                array(),
                $this->getContainerBuilder()
            )
        ;

        // @codeCoverageIgnoreStart
        if (null === $configuration) {
            throw new LogicException(
                "The extension \"$alias\" did not return any configuration."
            );
        }
        // @codeCoverageIgnoreEnd

        if ($current) {
            // @codeCoverageIgnoreStart
            if (!($configuration instanceof DefaultsInterface)) {
                throw new LogicException(
                    "The extension \"$alias\" does not implement DefaultsInterface."
                );
            }
            // @codeCoverageIgnoreEnd

            $configuration->mergeDefaultValues(
                $this
                    ->containerBuilder
                    ->getParameter(
                        $configuration->getParameterName()
                    )
            );
        }

        return $configuration;
    }

    /**
     * Returns the container builder.
     *
     * @return ContainerBuilder The container builder.
     */
    protected function getContainerBuilder()
    {
        if (null === $this->containerBuilder) {
            /** @var ContainerHelper $helper */
            $helper = $this->getHelper('container');
            $this->containerBuilder = $helper->getContainer();

            // @codeCoverageIgnoreStart
            if (!($this->containerBuilder instanceof ContainerBuilder)) {
                $this->containerBuilder = $helper->getContainerBuilder();
            }
            // @codeCoverageIgnoreEnd
        }

        return $this->containerBuilder;
    }

    /**
     * Returns the container extension.
     *
     * @param string $alias The alias for the extension.
     *
     * @return Extension The container extension.
     */
    protected function getExtension($alias)
    {
        return $this->getContainerBuilder()->getExtension($alias);
    }

    /**
     * Writes the formatted configuration to the output manager.
     *
     * @param OutputInterface        $output        The output manager.
     * @param string                 $format        The output format.
     * @param ConfigurationInterface $configuration The configuration.
     *
     * @throws InvalidArgumentException If the format is not supported.
     */
    protected function writeConfiguration(
        OutputInterface $output,
        $format,
        ConfigurationInterface $configuration
    ) {
        switch ($format) {
            case 'xml':
                $dumper = new XmlReferenceDumper();
                $helper = $this->getHelper('xml');

                break;

            case 'yaml':
                $dumper = new YamlReferenceDumper();
                $helper = $this->getHelper('yaml');

                break;

        // @codeCoverageIgnoreStart
            default:
                throw new InvalidArgumentException(
                    "The format \"$format\" is not supported."
                );
        }
        // @codeCoverageIgnoreEnd

        $output->write(
            $helper->colorize(
                $dumper->dump($configuration)
            )
        );
    }
}
