<?php

namespace Box\Component\Console\Tests\Loader;

use Box\Component\Console\Loader\Resource;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ResourceTest extends TestCase
{
    /**
     * Verifies that we can set and retrieve the resource information.
     *
     * @covers \Box\Component\Console\Loader\Resource
     */
    public function testResource()
    {
        $resource = new Resource('a', 'b');

        self::assertEquals('a', $resource->getResource());
        self::assertEquals('b', $resource->getType());
    }
}
