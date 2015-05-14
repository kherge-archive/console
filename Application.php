<?php

namespace Box\Component\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Basis for a console application assembled by dependency injection.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class Application
{
    /**
     * The application service identifier.
     *
     * @var string
     */
    const SERVICE_ID = 'box.console';

    /**
     * The container.
     *
     * @var ContainerInterface
     */
    private $container;

    /**
     * Sets the container.
     *
     * @param ContainerInterface $container The container.
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Returns the container.
     *
     * @return ContainerInterface The container.
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * Runs the console application.
     *
     * @param InputInterface  $input  The input manager.
     * @param OutputInterface $output The output manager.
     *
     * @return integer The exit status.
     */
    public function run(InputInterface $input, OutputInterface $output)
    {
        return $this
            ->container
            ->get(self::SERVICE_ID)
            ->run($input, $output)
        ;
    }
}
