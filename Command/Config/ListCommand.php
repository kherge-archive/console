<?php

namespace Box\Component\Console\Command\Config;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Extension\ConfigurationExtensionInterface;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Lists the registered extensions.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ListCommand extends AbstractConfigCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $definition = $this->getDefinition();
        $definition->setArguments(array());
        $definition->setOptions(array());

        $this
            ->setName('config:list')
            ->setDescription('Lists the registered extensions')
            ->setHelp(
                <<<HELP
The <comment>%command.name%</comment> command will list the aliases of all of
the registered extensions that define their own configuration. You can use this
information to either see the current <comment>(config:current)</comment> or
reference <comment>(config:reference)</comment> configuration information.
HELP
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainerBuilder();
        $extensions = $container->getExtensions();
        $configured = array();

        /** @var ConfigurationExtensionInterface|ExtensionInterface $extension */
        foreach ($extensions as $extension) {
            if ($extension instanceof ConfigurationExtensionInterface) {
                $configured[] = $extension->getAlias();
            }
        }

        if (0 === count($configured)) {
            $output->writeln('<fg=red>No extensions available.</fg=red>');
        } else {
            $output->writeln('<comment>Extensions:</comment>');
            $output->writeln('');

            foreach ($configured as $extension) {
                $output->writeln("  <info>-</info> $extension");
            }

            $output->writeln('');
        }
    }
}
