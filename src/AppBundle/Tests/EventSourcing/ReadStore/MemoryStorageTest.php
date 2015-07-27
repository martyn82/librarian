<?php

namespace AppBundle\Tests\EventSourcing\ReadStore;

use AppBundle\EventSourcing\ReadStore\Document;
use AppBundle\EventSourcing\ReadStore\MemoryStorage;

class MemoryStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testUpsertInsertsData()
    {
        $record = $this->getMockBuilder(Document::class)
            ->getMock();

        $storage = new MemoryStorage();
        $storage->upsert(1, $record);

        self::assertEquals($record, $storage->find(1));
    }

    public function testUpsertUpdatesData()
    {
        $initialRecord = $this->getMockBuilder(Document::class)
            ->getMock();

        $storage = new MemoryStorage();
        $storage->upsert(1, $initialRecord);

        $updatedRecord = $this->getMockBuilder(Document::class)
            ->getMock();

        $storage->upsert(1, $updatedRecord);

        self::assertEquals($updatedRecord, $storage->find(1));
    }

    public function testDeleteIsIdempotent()
    {
        $storage = new MemoryStorage();
        $storage->delete(1);

        self::assertTrue(true);
    }

    public function testDeleteWillDeleteRecord()
    {
        $initialRecord = $this->getMockBuilder(Document::class)
            ->getMock();

        $storage = new MemoryStorage();
        $storage->upsert(1, $initialRecord);

        $storage->delete(1);
        self::assertNull($storage->find(1));
    }

    public function testFindAllReturnsAll()
    {
        $initialRecord = $this->getMockBuilder(Document::class)
            ->getMock();

        $storage = new MemoryStorage();
        $storage->upsert(1, $initialRecord);

        self::assertEquals([$initialRecord], $storage->findAll());
    }

    public function testClearWillClearInternalStorage()
    {
        $initialRecord = $this->getMockBuilder(Document::class)
            ->getMock();

        $storage = new MemoryStorage();
        $storage->upsert(1, $initialRecord);
        $storage->clear();

        self::assertEquals([], $storage->findAll());
    }
}
