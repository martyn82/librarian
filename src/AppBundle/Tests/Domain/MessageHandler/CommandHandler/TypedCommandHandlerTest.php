<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\EventSourcing\Message\Command;
use AppBundle\EventSourcing\MessageHandler\CommandHandler;
use AppBundle\EventSourcing\MessageHandler\TypedCommandHandler;

class TypedCommandHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testTypedCommandHandlerThrowsExceptionIfMethodNotImplemented()
    {
        self::setExpectedException(\InvalidArgumentException::class);

        $command = $this->getMockBuilder(Command::class)
            ->getMock();

        $handler = new FakeTypedCommandHandler();
        $handler->handle($command);
    }
}

class FakeTypedCommandHandler implements CommandHandler
{
    use TypedCommandHandler;
}