<?php

namespace Box\Component\Console\Tests\Command\Debug;

use Box\Component\Console\Application;
use Box\Component\Console\ApplicationCache;
use Box\Component\Console\Test\CommandTestCase;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ContainerCommandTest extends CommandTestCase
{
    /**
     * Verifies that we can debug the service container.
     *
     * @covers \Box\Component\Console\Command\Debug\ContainerCommand
     * @covers \Box\Component\Console\Helper\ContainerHelper
     *
     * @runInSeparateProcess
     */
    public function testCommand()
    {
        // create a new application
        $this->application = ApplicationCache::bootstrap(
            $this->configDir . '/test.php'
        );

        // run the container debugger
        $this->runCommand(
            new ArrayInput(
                array(
                    'command' => 'debug:container'
                )
            ),
            $output
        );

        // verify that our service is listed
        $output = $this->readOutput($output);

        self::assertContains(Application::getId(), $output);

        // reload the application from the cache
        $this->application = ApplicationCache::bootstrap(
            $this->configDir . '/test.php'
        );

        // reset the output variable
        $output = null;

        // run the container debugger again
        $this->runCommand(
            new ArrayInput(
                array(
                    'command' => 'debug:container'
                )
            ),
            $output
        );

        // verify that our service is still listed
        $output = $this->readOutput($output);

        self::assertContains(Application::getId(), $output);
    }
}
