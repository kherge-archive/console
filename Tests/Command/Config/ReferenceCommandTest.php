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
class ReferenceCommandTest extends CommandTestCase
{
    /**
     * Verifies that we can display the reference configuration.
     *
     * @covers \Box\Component\Console\Command\Config\AbstractConfigCommand
     * @covers \Box\Component\Console\Command\Config\ReferenceCommand
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
            array()
        );

        // run the command
        $status = $this->runCommand(
            new ArrayInput(
                array(
                    'command' => 'config:reference',
                    'alias' => 'test'
                )
            ),
            $output
        );

        // verify that the current configuration is shown in yaml by default
        self::assertEquals(
            <<<CURRENT
test:

    # The alpha setting.
    alpha:                A

    # The beta setting.
    beta:                 2

    # The gamma setting.
    gamma:                true

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
                    'command' => 'config:reference',
                    'alias' => 'test',
                    '--format' => 'xml'
                )
            ),
            $output
        );

        // verify that the current configuration is shown in yaml by default
        self::assertEquals(
            <<<CURRENT
<!-- Namespace: http://example.org/schema/dic/test -->
<!-- alpha: The alpha setting. -->
<!-- beta: The beta setting. -->
<!-- gamma: The gamma setting. -->
<config
    alpha="A"
    beta="2"
    gamma="true"
/>

CURRENT
            ,
            $this->readOutput($output)
        );

        self::assertEquals(0, $status);
    }
}
