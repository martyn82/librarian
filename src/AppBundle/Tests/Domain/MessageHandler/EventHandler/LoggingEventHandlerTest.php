<?php

namespace AppBundle\Tests\Domain\MessageHandler\EventHandler;

use AppBundle\Domain\MessageHandler\EventHandler\LoggingEventHandler;
use AppBundle\Message\Event;
use AppBundle\MessageHandler\EventHandler;
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