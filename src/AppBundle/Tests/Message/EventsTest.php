<?php

namespace AppBundle\Tests\Message;

use AppBundle\Message\Event;
use AppBundle\Message\Events;

class EventsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructAcceptsArrayWithEventElements()
    {
        $mockEvent = $this->getMockBuilder(Event::class)
            ->getMock();

        $events = [
            $mockEvent
        ];

        $instance = new Events($events);

        self::assertCount(count($events), $instance->getIterator());
    }

    public function testConstructThrowsExceptionIfNotAllEventElements()
    {
        self::setExpectedException(\PHPUnit_Framework_Error::class);

        $events = [
            new \stdClass()
        ];

        $instance = new Events($events);
    }

    public function testAddAddsEventToList()
    {
        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $instance = new Events([]);
        $instance->add($event);

        self::assertCount(1, $instance->getIterator());
    }

    public function testClearWillMakeListEmpty()
    {
        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $instance = new Events([$event]);
        self::assertCount(1, $instance->getIterator());

        $instance->clear();
        self::assertCount(0, $instance->getIterator());
    }
}
