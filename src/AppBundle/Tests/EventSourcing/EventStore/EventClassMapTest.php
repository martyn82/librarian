<?php

namespace AppBundle\Tests\EventSourcing\EventStore;

use AppBundle\EventSourcing\EventStore\EventClassMap;
use AppBundle\EventSourcing\Message\Event;

class EventClassMapTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructWithMappingAddsToMap()
    {
        $list = [
            FooEvent::class,
            BarEvent::class
        ];

        $map = new EventClassMap($list);

        self::assertEquals(FooEvent::class, $map->getClassByEventName('FooEvent'));
        self::assertEquals(BarEvent::class, $map->getClassByEventName('BarEvent'));
    }
}

final class FooEvent extends Event {}
final class BarEvent extends Event {}
