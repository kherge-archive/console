<?php

namespace Box\Component\Console\Command\Debug;

use Box\Component\Console\Helper\ContainerHelper;
use Symfony\Bundle\FrameworkBundle\Command\ContainerDebugCommand;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Integrates the `debug:container` commands with the application.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ContainerCommand extends ContainerDebugCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        if (!in_array('debug:container', $this->getAliases(), true)) {
            $this->setAliases(array('debug:container'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerBuilder()
    {
        /** @var ContainerHelper $helper */
        $helper = $this->getHelper('container');
        $container = $helper->getContainer();

        if (!($container instanceof ContainerBuilder)) {
            $container = $helper->getContainerBuilder();
            $container->compile();
        }

        return $container;
    }
}
