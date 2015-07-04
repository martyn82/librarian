<?php

namespace AppBundle\Tests\Domain\Service;

use AppBundle\Domain\Service\LoggingEventHandler;
use AppBundle\EventStore\Event;
use AppBundle\Service\EventHandler;
use Psr\Log\LoggerInterface;

class LoggingEventHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleCallsLogger()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $inner = $this->getMockBuilder(EventHandler::class)
            ->getMock();

        $logger->expects(self::atLeastOnce())
            ->method('debug');

        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $handler = new LoggingEventHandler($logger, $inner);
        $handler->handle($event);
    }

    public function testHandlePropagatesToInnerHandler()
    {
        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $event = $this->getMockBuilder(Event::class)
            ->getMock();

        $inner = $this->getMockBuilder(EventHandler::class)
            ->getMock();

        $inner->expects(self::once())
            ->method('handle')
            ->with($event);

        $handler = new LoggingEventHandler($logger, $inner);
        $handler->handle($event);
    }
}
