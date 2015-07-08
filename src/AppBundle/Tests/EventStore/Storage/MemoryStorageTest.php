<?php

namespace AppBundle\Tests\EventStore\Storage;

use AppBundle\EventStore\Storage\MemoryStorage;

class MemoryStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testContainsReturnsFalseIfIdentityDoesNotExistInStorage()
    {
        $id = 'foo';
        $storage = new MemoryStorage();
        self::assertFalse($storage->contains($id));
    }

    public function testUpsertAddsRecordToStorageWhenIdentityDoesNotExist()
    {
        $id = 'foo';
        $storage = new MemoryStorage();
        self::assertFalse($storage->contains($id));

        $record = ['foo' => 'bar'];
        $storage->upsert($id, $record);

        self::assertTrue($storage->contains($id));
        self::assertEquals([$record], $storage->find($id));
    }

    public function testUpsertAppendsRecordToStorageWhenIdentityAlreadyExists()
    {
        $id = 'foo';
        $storage = new MemoryStorage();
        self::assertFalse($storage->contains($id));

        $initialRecord = ['foo' => 'bar'];
        $storage->upsert($id, $initialRecord);

        self::assertTrue($storage->contains($id));
        self::assertEquals([$initialRecord], $storage->find($id));

        $appendRecord = ['baz' => 'boo'];
        $storage->upsert($id, $appendRecord);

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
        $storage = new MemoryStorage();
        self::assertEquals([], $storage->find('foo'));
    }
}
