<?php

namespace Box\Component\Console\Tests\Loader;

use Box\Component\Console\Loader\ResourceSupport;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ResourceSupportTest extends TestCase
{
    /**
     * Verifies that we can set and retrieve the support resource information.
     *
     * @covers \Box\Component\Console\Loader\ResourceSupport
     */
    public function testResource()
    {
        $resource = new ResourceSupport('a', 'b', 'c', 'd');

        self::assertEquals('a', $resource->getResource());
        self::assertEquals('b', $resource->getSupportResource());
        self::assertEquals('c', $resource->getType());
        self::assertEquals('d', $resource->getSupportType());
    }
}
