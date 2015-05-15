<?php

namespace Box\Component\Console\Command\Config;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Displays the current configuration for an extension.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class CurrentCommand extends AbstractConfigCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this
            ->setName('config:current')
            ->setDescription('Displays the current configuration')
            ->setHelp(
                <<<HELP
The <comment>%command.name%</comment> command will display the current
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
            $this->getConfiguration(
                $input->getArgument('alias'),
                true
            )
        );
    }
}
