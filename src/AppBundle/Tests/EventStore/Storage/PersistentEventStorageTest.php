<?php

namespace AppBundle\Tests\EventStore\Storage;

use AppBundle\EventStore\EventDescriptor;
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
        $identityField = 'identity';

        $collection = $this->getCollection();

        $collection->expects(self::once())
            ->method('count')
            ->with([$identityField => $identity]);

        $storage = new PersistentEventStorage($collection, $identityField);
        $storage->contains($identity);
    }

    public function testFindCallsFindOnCollection()
    {
        $identity = 1;
        $identityField = 'identity';
        $collection = $this->getCollection();

        $cursor = $this->getMockBuilder(Cursor::class)
            ->disableOriginalConstructor()
            ->getMock();

        $collection->expects(self::once())
            ->method('find')
            ->with([$identityField => $identity])
            ->will(self::returnValue($cursor));

        $storage = new PersistentEventStorage($collection, $identityField);
        $storage->find($identity);
    }

    public function testFindReconstructsEventDescriptors()
    {
        $identity = 1;
        $identityField = 'identity';
        $collection = $this->getCollection();

        $eventData = [
            EventDescriptor::record($identity, 'foo', '[]', -1)->toArray()
        ];
        $cursor = new FakeCursor($eventData);

        $collection->expects(self::once())
            ->method('find')
            ->with([$identityField => $identity])
            ->will(self::returnValue($cursor));

        $storage = new PersistentEventStorage($collection, $identityField);
        $events = $storage->find($identity);

        self::assertCount(1, $events);
        self::assertEquals($eventData[0], $events[0]->toArray());
    }

    public function testAppendCallsUpsertOnCollection()
    {
        $identity = 1;
        $identityField = 'identity';
        $event = EventDescriptor::record($identity, 'foo', '[]', -1);

        $collection = $this->getCollection();

        $collection->expects(self::once())
            ->method('insert')
            ->with($event->toArray());

        $storage = new PersistentEventStorage($collection, $identityField);
        $storage->append($event);
    }
}

class FakeCursor extends \ArrayObject {}
