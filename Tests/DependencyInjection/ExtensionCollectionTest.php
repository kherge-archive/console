<?php

namespace Box\Component\Console\Tests\DependencyInjection;

use Box\Component\Console\DependencyInjection\ExtensionCollection;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

/**
 * Verifies that the class functions as intended.
 *
 * @author Kevin Herrera <kevin@herrera.io>
 */
class ExtensionCollectionTest extends TestCase
{
    /**
     * The extension collection instance being tested.
     *
     * @var ExtensionCollection
     */
    private $collection;

    /**
     * The mock extension.
     *
     * @var ExtensionInterface|MockObject
     */
    private $extension;

    /**
     * Verifies that the attach method returns the instance.
     *
     * @covers \Box\Component\Console\DependencyInjection\ExtensionCollection::attach
     */
    public function testAttach()
    {
        self::assertSame(
            $this->collection,
            $this->collection->attach($this->extension)
        );
    }

    /**
     * Verifies that container extensions are supported.
     *
     * @covers \Box\Component\Console\DependencyInjection\ExtensionCollection::isSupported
     */
    public function testIsSupported()
    {
        self::assertTrue($this->collection->isSupported($this->extension));
    }

    /**
     * Creates a new mock extension.
     */
    protected function setUp()
    {
        $this->collection = new ExtensionCollection();
        $this->extension = $this
            ->getMockBuilder('Symfony\Component\DependencyInjection\Extension\ExtensionInterface')
            ->getMockForAbstractClass()
        ;
    }
}
