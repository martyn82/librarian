<?php

namespace AppBundle\Tests\EventStore\Storage;

use AppBundle\EventStore\EventDescriptor;
use AppBundle\EventStore\EventStore;
use AppBundle\EventStore\Storage\MemoryEventStorage;

class MemoryEventStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testContainsReturnsFalseIfIdentityDoesNotExistInStorage()
    {
        $id = 'foo';
        $storage = new MemoryEventStorage();
        self::assertFalse($storage->contains($id));
    }

    public function testAppendAddsRecordToStorageWhenIdentityDoesNotExist()
    {
        $id = 'foo';
        $storage = new MemoryEventStorage();
        self::assertFalse($storage->contains($id));

        $record = EventDescriptor::record($id, 'foo', '[bar]', EventStore::FIRST_VERSION);
        $storage->append($record);

        self::assertTrue($storage->contains($id));
        self::assertEquals([$record], $storage->find($id));
    }

    public function testAppendAppendsRecordToStorageWhenIdentityAlreadyExists()
    {
        $id = 'foo';
        $storage = new MemoryEventStorage();
        self::assertFalse($storage->contains($id));

        $initialRecord = EventDescriptor::record($id, 'foo', '["foo":"bar"]', EventStore::FIRST_VERSION);
        $storage->append($initialRecord);

        self::assertTrue($storage->contains($id));
        self::assertEquals([$initialRecord], $storage->find($id));

        $appendRecord = EventDescriptor::record($id, 'foo', '["baz":"boo"]', EventStore::FIRST_VERSION);
        $storage->append($appendRecord);

        self::assertEquals(
            [
                $initialRecord,
                $appendRecord
            ],
            $storage->find($id)
        );
    }

    public function testFindWillReturnEmptyResultIfIdentityDoesNotExist()
    {
        $storage = new MemoryEventStorage();
        self::assertEquals([], $storage->find('foo'));
    }

    public function testFindIdentitiesWillReturnAllIds()
    {
        $storage = new MemoryEventStorage();
        $storage->append(EventDescriptor::record('a', 'foo', 'bar', 1));
        self::assertEquals(['a'], $storage->findIdentities());
    }
}
