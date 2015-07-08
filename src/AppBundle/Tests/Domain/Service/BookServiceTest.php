<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\ReadModel\Book;
use AppBundle\EventStore\Guid;
use AppBundle\MessageBus\CommandBus;
use AppBundle\Message\Command;

class BookServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGetBookCallsGetBookOnReadModel()
    {
        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = $this->getMockBuilder(Book::class)
            ->disableOriginalConstructor()
            ->getMock();

        $id = Guid::createNew();

        $readModel->expects(self::once())
            ->method('getBook')
            ->with($id);

        $service = new BookService($readModel, $commandBus);
        $service->getBook($id);
    }

    public function testExecuteCallsHandleOnCommandBus()
    {
        $readModel = $this->getMockBuilder(Book::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $command = $this->getMockBuilder(Command::class)
            ->getMock();

        $commandBus->expects(self::once())
            ->method('handle')
            ->with($command);

        $service = new BookService($readModel, $commandBus);
        $service->execute($command);
    }
}
