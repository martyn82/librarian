<?php

namespace AppBundle\Tests\EventStore\Storage;

use AppBundle\EventStore\EventDescriptor;
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

        $record = EventDescriptor::record($id, 'foo', '[bar]');
        $storage->append($record);

        self::assertTrue($storage->contains($id));
        self::assertEquals([$record], $storage->find($id));
    }

    public function testAppendAppendsRecordToStorageWhenIdentityAlreadyExists()
    {
        $id = 'foo';
        $storage = new MemoryEventStorage();
        self::assertFalse($storage->contains($id));

        $initialRecord = EventDescriptor::record($id, 'foo', '["foo":"bar"]');
        $storage->append($initialRecord);

        self::assertTrue($storage->contains($id));
        self::assertEquals([$initialRecord], $storage->find($id));

        $appendRecord = EventDescriptor::record($id, 'foo', '["baz":"boo"]');
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
}
