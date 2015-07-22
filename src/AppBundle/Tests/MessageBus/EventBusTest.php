<?php

namespace AppBundle\Tests\MessageBus;

use AppBundle\Message\Event;
use AppBundle\MessageBus\EventBus;
use AppBundle\MessageHandler\EventHandler;

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
