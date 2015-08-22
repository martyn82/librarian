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

    public function testFindByCriteriaReturnsOnlyMatchingDocuments()
    {
        $record1 = $this->getMockBuilder(MemoryFakeDocument::class)
            ->getMock();

        $record1->expects(self::any())
            ->method('getFoo')
            ->will(self::returnValue('foo'));

        $record1->expects(self::any())
            ->method('getBar')
            ->will(self::returnValue('bar'));

        $record2 = $this->getMockBuilder(MemoryFakeDocument::class)
            ->getMock();

        $record2->expects(self::any())
            ->method('getFoo')
            ->will(self::returnValue('foo'));

        $record2->expects(self::any())
            ->method('getBar')
            ->will(self::returnValue('baz'));

        $storage = new MemoryStorage();
        $storage->upsert(1, $record1);
        $storage->upsert(2, $record2);

        $matches = $storage->findBy(['foo' => 'foo', 'bar' => 'bar']);
        self::assertCount(1, $matches);
    }
}

class MemoryFakeDocument extends Document
{
    /**
     * @return string
     */
    final public function getId()
    {
        return '1';
    }

    /**
     * @return integer
     */
    final public function getVersion()
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getFoo()
    {
        return 'foo';
    }

    /**
     * @return string
     */
    public function getBar()
    {
        return 'bar';
    }

    /**
     * @param array $data
     * @return FakeDocument
     */
    public static function deserialize(array $data)
    {
        return new self();
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [];
    }
}
