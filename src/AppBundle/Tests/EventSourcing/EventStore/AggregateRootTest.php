<?php

namespace AppBundle\Tests\EventSourcing\EventStore;

use AppBundle\EventSourcing\EventStore\AggregateRoot;
use AppBundle\EventSourcing\EventStore\UnsupportedEventException;
use AppBundle\EventSourcing\Message\Event;
use AppBundle\EventSourcing\Message\Events;

class AggregateRootTest extends \PHPUnit_Framework_TestCase
{
    public function testGetUncomittedChangesReturnsCompleteEventLog()
    {
        $root = new FakeAggregateRoot();
        $events = $root->getUncommittedChanges();

        self::assertInstanceOf(Events::class, $events);
    }

    public function testMarkChangesCommittedClearsEventLog()
    {
        $root = new FakeAggregateRoot();
        $root->test();

        $events = $root->getUncommittedChanges();
        self::assertCount(1, $events->getIterator());

        $root->markChangesCommitted();
        $events = $root->getUncommittedChanges();

        self::assertInstanceOf(Events::class, $events);
        self::assertCount(0, $events->getIterator());
    }

    public function testAttemptToApplyANonSupportedEventThrowsException()
    {
        $eventClass = UnsupportedEvent::class;
        $eventClassParts = explode('\\', $eventClass);
        $eventName = end($eventClassParts);
        $aggregateClass = FakeAggregateRoot::class;

        self::setExpectedException(
            UnsupportedEventException::class,
            "Event '{$eventName}' not supported for aggregate '{$aggregateClass}'."
        );

        $root = new FakeAggregateRoot();
        $root->unsupported();
    }

    public function testLoadFromHistoryAppliesEventsFromHistory()
    {
        $history = new Events(
            [
                new TestEvent(),
                new TestEvent()
            ]
        );

        $root = new FakeAggregateRoot();
        $root->loadFromHistory($history);

        self::assertEquals($history->getIterator()->count(), $root->getTestEventApplyCount());
    }
}

class TestEvent extends Event {}
class UnsupportedEvent extends Event {}

class FakeAggregateRoot extends AggregateRoot
{
    /**
     * @var integer
     */
    private $testEventApplyCount = 0;

    /**
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return null
     */
    public function getId()
    {
        return null;
    }

    /**
     */
    public function test()
    {
        $this->applyChange(new TestEvent());
    }

    /**
     */
    public function unsupported()
    {
        $this->applyChange(new UnsupportedEvent());
    }

    /**
     * @param TestEvent $event
     */
    protected function applyTestEvent(TestEvent $event)
    {
        $this->testEventApplyCount++;
    }

    /**
     * @return integer
     */
    public function getTestEventApplyCount()
    {
        return $this->testEventApplyCount;
    }
}
