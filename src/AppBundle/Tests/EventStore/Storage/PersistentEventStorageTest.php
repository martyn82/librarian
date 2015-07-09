<?php

namespace AppBundle\Tests\EventStore\Storage;

use AppBundle\EventStore\Storage\PersistentEventStorage;
use Doctrine\MongoDB\Collection;
use Doctrine\MongoDB\Connection;
use Doctrine\MongoDB\Cursor;

class PersistentEventStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Connection
     */
    private function getConnection()
    {
        return $this->getMockBuilder(Connection::class)
            ->getMock();
    }

    /**
     * @return Collection
     */
    private function getCollection()
    {
        return $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testConnectionIsClosedOnDestruction()
    {
        $connection = $this->getConnection();
        $connection->expects(self::once())
            ->method('close');

        $storage = new PersistentEventStorage($connection, 'type');
        $storage = null;
    }

    public function testContainsCallsCountOnCollection()
    {
        $identity = 1;

        $collection = $this->getCollection();
        $connection = $this->getConnection();

        $connection->expects(self::once())
            ->method('selectCollection')
            ->will(self::returnValue($collection));

        $collection->expects(self::once())
            ->method('count')
            ->with(['identity' => $identity]);

        $storage = new PersistentEventStorage($connection, 'type');
        $storage->contains($identity);
    }

    public function testFindCallsFindOnCollection()
    {
        $identity = 1;

        $collection = $this->getCollection();
        $connection = $this->getConnection();

        $cursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $connection->expects(self::once())
            ->method('selectCollection')
            ->will(self::returnValue($collection));

        $collection->expects(self::once())
            ->method('find')
            ->with(['identity' => $identity])
            ->will(self::returnValue($cursor));

        $storage = new PersistentEventStorage($connection, 'type');
        $storage->find($identity);
    }

    public function testAppendCallsUpsertOnCollection()
    {
        $identity = 1;
        $data = ['foo' => 'bar'];

        $collection = $this->getCollection();
        $connection = $this->getConnection();

        $connection->expects(self::once())
            ->method('selectCollection')
            ->will(self::returnValue($collection));

        $collection->expects(self::once())
            ->method('upsert')
            ->with(['identity' => $identity], $data);

        $storage = new PersistentEventStorage($connection, 'type');
        $storage->append($identity, $data);
    }
}
