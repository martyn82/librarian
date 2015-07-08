<?php

namespace AppBundle\Tests\EventStore\Storage;

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

        $record = ['foo' => 'bar'];
        $storage->append($id, $record);

        self::assertTrue($storage->contains($id));
        self::assertEquals([$record], $storage->find($id));
    }

    public function testAppendAppendsRecordToStorageWhenIdentityAlreadyExists()
    {
        $id = 'foo';
        $storage = new MemoryEventStorage();
        self::assertFalse($storage->contains($id));

        $initialRecord = ['foo' => 'bar'];
        $storage->append($id, $initialRecord);

        self::assertTrue($storage->contains($id));
        self::assertEquals([$initialRecord], $storage->find($id));

        $appendRecord = ['baz' => 'boo'];
        $storage->append($id, $appendRecord);

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
