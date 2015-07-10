<?php

namespace AppBundle\Tests\EventStore\Storage;

use AppBundle\EventStore\Storage\PersistentEventStorage;
use Doctrine\MongoDB\Collection;
use Doctrine\MongoDB\Cursor;

class PersistentEventStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return Collection
     */
    private function getCollection()
    {
        return $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testContainsCallsCountOnCollection()
    {
        $identity = 1;

        $collection = $this->getCollection();

        $collection->expects(self::once())
            ->method('count')
            ->with(['identity' => $identity]);

        $storage = new PersistentEventStorage($collection);
        $storage->contains($identity);
    }

    public function testFindCallsFindOnCollection()
    {
        $identity = 1;

        $collection = $this->getCollection();

        $cursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collection->expects(self::once())
            ->method('find')
            ->with(['identity' => $identity])
            ->will(self::returnValue($cursor));

        $storage = new PersistentEventStorage($collection);
        $storage->find($identity);
    }

    public function testAppendCallsUpsertOnCollection()
    {
        $identity = 1;
        $data = ['foo' => 'bar'];

        $collection = $this->getCollection();

        $collection->expects(self::once())
            ->method('insert')
            ->with($data);

        $storage = new PersistentEventStorage($collection);
        $storage->append($identity, $data);
    }
}
