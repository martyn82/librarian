<?php

namespace AppBundle\Tests\Collections;

use AppBundle\Collections\BasicCollection;

class BasicCollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider elementProvider
     *
     * @param mixed $element
     */
    public function testAddElementChangesCollection($element)
    {
        $collection = new BasicCollection();
        $result = $collection->add($element);

        self::assertTrue($result);
        self::assertEquals(1, $collection->size());

        $elements = $collection->toArray();
        self::assertEquals($element, $elements[0]);
    }

    /**
     * @return array
     */
    public function elementProvider()
    {
        return [
            ['foo'],
            [6781],
            [true],
            [false],
            [null],
            [.43],
            [new \stdClass()],
            [['foo' => 121]]
        ];
    }

    public function testAddAllElementsChangesCollection()
    {
        $collectionA = new BasicCollection();
        $collectionA->add('foo');
        $collectionA->add('bar');
        $collectionA->add('baz');

        $collectionB = new BasicCollection();
        $result = $collectionB->addAll($collectionA);

        self::assertTrue($result);
        self::assertEquals(3, $collectionB->size());
    }

    public function testClearMakesCollectionEmpty()
    {
        $collection = new BasicCollection();
        $collection->add('foo');
        $collection->clear();

        self::assertEquals(0, $collection->size());
    }

    public function testContainsElement()
    {
        $element = 'foo';

        $collection = new BasicCollection();
        $collection->add($element);

        self::assertTrue($collection->contains($element));
    }

    public function testNotContainsElement()
    {
        $element = 'foo';
        $collection = new BasicCollection();
        self::assertFalse($collection->contains($element));
    }

    public function testContainsAllElements()
    {
        $collectionA = new BasicCollection();
        $collectionA->add('foo');
        $collectionA->add('bar');
        $collectionA->add('foo');

        $collectionB = new BasicCollection();
        $collectionB->add('bar');
        $collectionB->add('baz');
        $collectionB->add('foo');

        self::assertTrue($collectionB->containsAll($collectionA));
        self::assertFalse($collectionA->containsAll($collectionB));
    }

    public function testIsEmpty()
    {
        $collection = new BasicCollection();
        self::assertTrue($collection->isEmpty());

        $collection->add('foo');
        self::assertFalse($collection->isEmpty());
    }

    public function testGetIterator()
    {
        $collection = new BasicCollection();
        $iterator = $collection->getIterator();
        self::assertInstanceOf(\Iterator::class, $iterator);
    }

    public function testRemoveElement()
    {
        $collection = new BasicCollection();
        $result = $collection->remove('foo');

        self::assertFalse($result);

        $collection->add('foo');
        $result = $collection->remove('foo');

        self::assertTrue($result);
        self::assertEquals(0, $collection->size());
    }

    public function testRemoveAllElements()
    {
        $collectionA = new BasicCollection();
        $collectionA->add('foo');
        $collectionA->add('bar');

        $collectionB = new BasicCollection();
        $collectionB->add('foo');
        $collectionB->add('foo');
        $collectionB->add('baz');

        self::assertTrue($collectionA->removeAll($collectionB));
        self::assertEquals(1, $collectionA->size());

        self::assertFalse($collectionB->removeAll($collectionA));
    }

    public function testRetainAllElements()
    {
        $collectionA = new BasicCollection();
        $collectionA->add('foo');
        $collectionA->add('bar');

        $collectionB = new BasicCollection();
        $collectionB->add('foo');

        self::assertTrue($collectionA->retainAll($collectionB));
        self::assertFalse($collectionA->retainAll($collectionB));

        $elements = $collectionA->toArray();
        self::assertEquals('foo', $elements[0]);
    }

    public function testSize()
    {
        $collection = new BasicCollection();
        self::assertEquals(0, $collection->size());

        $collection->add('foo');
        self::assertEquals(1, $collection->size());
    }

    public function testConvertToArray()
    {
        $collection = new BasicCollection();
        $collection->add('foo');
        $collection->add('bar');
        $collection->add('baz');

        $collection->remove('bar'); // this call should not create a gap in the index sequence

        $actual = $collection->toArray();
        $expected = [
            0 => 'foo',
            1 => 'baz'
        ];

        self::assertEquals($expected, $actual);
    }
}
