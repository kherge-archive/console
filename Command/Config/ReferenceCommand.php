<?php

namespace Box\Component\Console\Command\Config;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Displays a configuration reference for an extension.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ReferenceCommand extends AbstractConfigCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('config:reference')
            ->setDescription('Displays a configuration reference')
            ->setHelp(
                <<<HELP
The <comment>%command.name%</comment> command will display the reference
configuration for an extension with the given <info>alias</info>.
HELP
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->writeConfiguration(
            $output,
            $input->getOption('format'),
            $this->getConfiguration($input->getArgument('alias'))
        );
    }
}
