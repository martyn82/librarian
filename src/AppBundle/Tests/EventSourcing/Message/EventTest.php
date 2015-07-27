<?php

namespace AppBundle\Tests\EventSourcing\Message;

use AppBundle\EventSourcing\Message\Event;

class EventTest extends \PHPUnit_Framework_TestCase
{
    public function testGetEventNameReturnsNameOfEvent()
    {
        self::assertEquals('FakeEvent', FakeEvent::getName());

        $instance = new FakeEvent();
        self::assertEquals('FakeEvent', $instance->getEventName());
    }
}

class FakeEvent extends Event {}
