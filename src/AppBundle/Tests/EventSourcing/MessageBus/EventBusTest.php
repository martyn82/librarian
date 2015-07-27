<?php

namespace AppBundle\Tests\EventSourcing\MessageBus;

use AppBundle\EventSourcing\Message\Event;
use AppBundle\EventSourcing\MessageBus\EventBus;
use AppBundle\EventSourcing\MessageHandler\EventHandler;

class EventBusTest extends \PHPUnit_Framework_TestCase
{
    public function testPublishPropagatesEventToRegisteredHandlers()
    {
        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $handler = $this->getMockBuilder(EventHandler::class)
            ->getMock();

        $handler->expects(self::once())
            ->method('on')
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
