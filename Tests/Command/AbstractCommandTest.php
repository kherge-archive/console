<?php

namespace Box\Component\Console\Tests\Command;

use Box\Component\Console\Command\AbstractCommand;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @covers \Box\Component\Console\Command\AbstractCommand
 */
class AbstractCommandTest extends TestCase
{
    /**
     * The mock of the abstract command.
     *
     * @var AbstractCommand|MockObject
     */
    private $command;

    /**
     * Verifies that the processed help is wrapped properly.
     */
    public function testGetProcessedHelp()
    {
        $this->command->setHelp(
            <<<HELP
The processed help text for the <comment>%command.name%</comment> command should
be properly wrapped. This means that single sentences broken across multiple
lines should be unbroken and then subsequently wrapped.

Multiple spaces aren't affected.
HELP
        );

        self::assertEquals(
            <<<HELP
The processed help text for the <comment>mock</comment> command should be properly wrapped. This means
that single sentences broken across multiple lines should be unbroken and then subsequently
wrapped.

Multiple spaces aren't affected.
HELP
            ,
            $this->command->getProcessedHelp()
        );
    }

    /**
     * Creates a new mock of the abstract command.
     */
    protected function setUp()
    {
        $this->command = $this
            ->getMockBuilder('Box\Component\Console\Command\AbstractCommand')
            ->setConstructorArgs(array('mock'))
            ->getMockForAbstractClass()
        ;
    }
}
