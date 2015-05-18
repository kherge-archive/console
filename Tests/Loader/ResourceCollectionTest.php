<?php

namespace Box\Component\Console\Tests\Loader;

use Box\Component\Console\Loader\Resource;
use Box\Component\Console\Loader\ResourceCollection;
use PHPUnit_Framework_TestCase as TestCase;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ResourceCollectionTest extends TestCase
{
    /**
     * Verifies that we can create a new collection.
     *
     * @covers \Box\Component\Console\Loader\ResourceCollection
     */
    public function testCollection()
    {
        $resource = new Resource('test');
        $collection = new ResourceCollection(
            array(
                $resource
            )
        );

        self::assertTrue($collection->contains($resource));
    }
}
