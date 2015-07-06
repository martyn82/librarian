<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\MessageHandler\CommandHandler\LoggingDecorator;
use AppBundle\Message\Command;
use AppBundle\MessageHandler\CommandHandler;
use Psr\Log\LoggerInterface;

class LoggingDecoratorTest extends \PHPUnit_Framework_TestCase
{
    public function testDecoratedHandlerWillBeCalledOnHandle()
    {
        $command = $this->getMockBuilder(Command::class)
            ->getMock();

        $inner = $this->getMockBuilder(CommandHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();

        $inner->expects(self::once())
            ->method('handle')
            ->with($command);

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $handler = new LoggingDecorator($logger, $inner);
        $handler->handle($command);
    }

    public function testHandleCallsLoggerOnHandle()
    {
        $inner = $this->getMockBuilder(CommandHandler::class)
            ->getMock();

        $logger = $this->getMockBuilder(LoggerInterface::class)
            ->getMock();

        $logger->expects(self::atLeastOnce())
            ->method('debug');

        $command = $this->getMockBuilder(Command::class)
            ->getMock();

        $handler = new LoggingDecorator($logger, $inner);
        $handler->handle($command);
    }
}
