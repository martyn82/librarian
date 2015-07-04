<?php

namespace AppBundle\Tests\Domain\CommandHandler;

use AppBundle\Domain\CommandHandler\LoggingCommandHandler;
use AppBundle\Service\Command;
use AppBundle\Service\CommandHandler;
use Psr\Log\LoggerInterface;

class LoggingCommandHandlerTest extends \PHPUnit_Framework_TestCase
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

        $handler = new LoggingCommandHandler($logger, $inner);
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

        $handler = new LoggingCommandHandler($logger, $inner);
        $handler->handle($command);
    }
}
