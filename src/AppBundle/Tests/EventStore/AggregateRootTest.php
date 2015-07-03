<?php

namespace AppBundle\Tests\EventStore;

use AppBundle\EventStore\AggregateRoot;
use AppBundle\EventStore\Events;
use AppBundle\EventStore\UnsupportedEventException;
use AppBundle\Message\Event;

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
     * @var int
     */
    private $testEventApplyCount = 0;

    /**
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see \AppBundle\EventStore\AggregateRoot::getId()
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
     * @return int
     */
    public function getTestEventApplyCount()
    {
        return $this->testEventApplyCount;
    }
}
