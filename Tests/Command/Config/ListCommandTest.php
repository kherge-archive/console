<?php

namespace Box\Component\Console\Tests\Command\Config;

use Box\Component\Console\Test\CommandTestCase;
use Box\Component\Console\Tests\DependencyInjection\Extension;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @covers \Box\Component\Console\Command\Config\ListCommand
 */
class ListCommandTest extends CommandTestCase
{
    /**
     * Verifies that we can display the current configuration.
     */
    public function testCommand()
    {
        // register the test extension
        $extension = new Extension();

        $this->container->registerExtension($extension);
        $this->container->loadFromExtension(
            $extension->getAlias(),
            array(
                'alpha' => 1,
                'beta' => 'b',
                'gamma' => false
            )
        );

        // run the command again
        $status = $this->runCommand(
            new ArrayInput(
                array(
                    'command' => 'config:list'
                )
            ),
            $output
        );

        // verify that the registered extension is shown
        self::assertEquals(
            <<<CURRENT
Extensions:

  - test


CURRENT
            ,
            $this->readOutput($output)
        );

        self::assertEquals(0, $status);
    }

    /**
     * Verifies that we notify the user if no extensions are available.
     */
    public function testCommandNoneAvailable()
    {
        // run the command
        $status = $this->runCommand(
            new ArrayInput(
                array(
                    'command' => 'config:list'
                )
            ),
            $output
        );

        // verify that a message is shown for no extensions
        self::assertEquals(
            "No extensions available.\n",
            $this->readOutput($output)
        );

        self::assertEquals(0, $status);
    }
}
