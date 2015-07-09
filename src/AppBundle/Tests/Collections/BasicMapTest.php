<?php

namespace AppBundle\Tests\Collections;

use AppBundle\Collections\BasicMap;
use AppBundle\Collections\Map\EntrySet;

class BasicMapTest extends \PHPUnit_Framework_TestCase
{
    public function testClearMapClearsTheMap()
    {
        $map = new BasicMap();

        $map->put('key', 'value');
        self::assertEquals(1, $map->size());

        $map->clear();
        self::assertEquals(0, $map->size());
    }

    public function testContainsKeyReturnsTrueIfKeyIsContained()
    {
        $map = new BasicMap();
        $map->put('key', 'value');

        self::assertTrue($map->containsKey('key'));
        self::assertFalse($map->containsKey('foo'));
    }

    public function testContainsValueReturnsTrueIfValueIsContained()
    {
        $map = new BasicMap();
        $map->put('key', 'value');

        self::assertTrue($map->containsValue('value'));
        self::assertFalse($map->containsValue('foo'));
    }

    public function testEntrySetReturnsCopyOfInnerSetOfEntries()
    {
        $map = new BasicMap();
        $map->put('key', 'value');

        $entries = $map->entrySet();
        self::assertInstanceOf(EntrySet::class, $entries);
        self::assertEquals(1, $entries->size());

        $map->clear();
        self::assertEquals(1, $entries->size());
    }

    public function testGetReturnsValueIfExists()
    {
        $map = new BasicMap();
        $map->put('key', 'value');

        $value = $map->get('key');
        self::assertEquals('value', $value);

        self::assertNull($map->get('foo'));
    }

    public function testIsEmptyReturnsTrueIfMapIsEmpty()
    {
        $map = new BasicMap();
        self::assertTrue($map->isEmpty());

        $map->put('key', 'value');
        self::assertFalse($map->isEmpty());
    }

    public function testPutInsertsOrOverwritesItemInMap()
    {
        $map = new BasicMap();
        $map->put('key', 'value');

        self::assertEquals('value', $map->get('key'));

        $oldValue = $map->put('key', 'foo');

        self::assertEquals('value', $oldValue);
        self::assertEquals('foo', $map->get('key'));
    }

    public function testRemoveRemovesItemFromMap()
    {
        $map = new BasicMap();
        $map->put('key', 'value');
        $oldValue = $map->remove('key');

        self::assertEquals('value', $oldValue);
        self::assertTrue($map->isEmpty());

        self::assertNull($map->remove('foo'));
    }

    public function testSizeReturnsNumberOfItemsInMap()
    {
        $map = new BasicMap();
        $map->put('key', 'value');
        self::assertEquals(1, $map->size());

        $map->put('foo', 'bar');
        self::assertEquals(2, $map->size());
    }

    public function testPutAllAddsAllEntriesOfGivenMapIntoMap()
    {
        $map1 = new BasicMap();
        $map1->put('key', 'value');
        $map1->put('foo', 'bar');

        $map2 = new BasicMap();
        $map2->putAll($map1);

        self::assertEquals($map1->size(), $map2->size());
        self::assertFalse($map2->isEmpty());

        self::assertEquals('value', $map2->get('key'));
        self::assertEquals('bar', $map2->get('foo'));
    }
}
