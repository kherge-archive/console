<?php

namespace Box\Component\Console\Tests\Command\Config;

use Box\Component\Console\Test\CommandTestCase;
use Box\Component\Console\Tests\DependencyInjection\Extension;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class CurrentCommandTest extends CommandTestCase
{
    /**
     * Verifies that we can display the current configuration.
     *
     * @covers \Box\Component\Console\Command\Config\AbstractConfigCommand
     * @covers \Box\Component\Console\Command\Config\CurrentCommand
     * @covers \Box\Component\Console\Command\AbstractCommand
     * @covers \Box\Component\Console\DependencyInjection\Configuration\DefaultsInterface
     * @covers \Box\Component\Console\Helper\XmlHelper
     * @covers \Box\Component\Console\Helper\YamlHelper
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

        // run the command
        $status = $this->runCommand(
            new ArrayInput(
                array(
                    'command' => 'config:current',
                    'alias' => 'test'
                )
            ),
            $output
        );

        // verify that the current configuration is shown in yaml by default
        self::assertEquals(
            <<<CURRENT

# The test configuration settings.
test:

    # The alpha setting.
    alpha:                1

    # The beta setting.
    beta:                 b

    # The gamma setting.
    gamma:                false

CURRENT
            ,
            $this->readOutput($output)
        );

        self::assertEquals(0, $status);

        // reset the output
        $output = null;

        // run the command again
        $status = $this->runCommand(
            new ArrayInput(
                array(
                    'command' => 'config:current',
                    'alias' => 'test',
                    '--format' => 'xml'
                )
            ),
            $output
        );

        // verify that the current configuration is shown in yaml by default
        self::assertEquals(
            <<<CURRENT
<!-- The test configuration settings. -->
<!-- Namespace: http://example.org/schema/dic/test -->
<!-- alpha: The alpha setting. -->
<!-- beta: The beta setting. -->
<!-- gamma: The gamma setting. -->
<config
    alpha="1"
    beta="b"
    gamma="false"
/>

CURRENT
            ,
            $this->readOutput($output)
        );

        self::assertEquals(0, $status);
    }
}
