<?php

namespace Box\Component\Console\Tests\Test;

use Box\Component\Console\Test\CommandTestCase;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 *
 * @covers \Box\Component\Console\Test\CommandTestCase
 */
class CommandTestCaseTest extends CommandTestCase
{
    /**
     * Verifies that we can run a command and read the output from a stream.
     */
    public function testRunCommand()
    {
        self::assertEquals(
            0,
            $this->runCommand(
                new ArrayInput(array()),
                $output
            )
        );

        self::assertInstanceOf(
            'Symfony\Component\Console\Output\StreamOutput',
            $output
        );

        self::assertContains('help', $this->readOutput($output));
    }
}
