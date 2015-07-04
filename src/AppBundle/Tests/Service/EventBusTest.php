<?php

namespace AppBundle\Tests\Service;

use AppBundle\EventStore\Event;
use AppBundle\Service\EventBus;
use AppBundle\Service\EventHandler;

class EventBusTest extends \PHPUnit_Framework_TestCase
{
    public function testPublishPropagatesEventToRegisteredHandlers()
    {
        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $handler = $this->getMockBuilder(EventHandler::class)
            ->getMock();

        $handler->expects(self::once())
            ->method('handle')
            ->with($event);

        $eventBus = new EventBus(
            [
                get_class($event) => [$handler]
            ]
        );

        $eventBus->publish($event);
    }

    public function testPublishWithoutHandlerDoesNotDoAnything()
    {
        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $eventBus = new EventBus([]);
        $eventBus->publish($event);
    }
}
