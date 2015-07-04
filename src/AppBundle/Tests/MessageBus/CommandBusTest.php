<?php

namespace AppBundle\Tests\MessageBus;

use AppBundle\Message\Command;
use AppBundle\MessageBus\CommandBus;
use AppBundle\MessageBus\NoCommandHandlerException;
use AppBundle\MessageHandler\CommandHandler\CommandHandler;

class CommandBusTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandBusHandleCommandCallsHandleOnAppropriateHandler()
    {
        $command = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();

        $handler = $this->getMockBuilder(CommandHandler::class)
            ->disableOriginalConstructor()
            ->setMethods(['handle'])
            ->getMock();

        $handler->expects(self::once())
            ->method('handle')
            ->with($command);

        $commandHandlerMap = [
            get_class($command) => $handler
        ];

        $commandBus = new CommandBus($commandHandlerMap);
        $commandBus->handle($command);
    }

    public function testCommandBusHandleCommandThrowsExceptionIfNoHandlerExist()
    {
        self::setExpectedException(NoCommandHandlerException::class);

        $command = $this->getMockBuilder(Command::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = new CommandBus([]);
        $commandBus->handle($command);
    }
}
