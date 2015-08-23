<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Aggregate\Book;
use AppBundle\Domain\Message\Command\CheckOutBook;
use AppBundle\Domain\MessageHandler\CommandHandler\CheckOutBookHandler;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\EventStore\Uuid;

class CheckOutBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testCheckOutBookHandlerWillCallStoreOnRepository()
    {
        $bookId = Uuid::createNew();
        $userId = Uuid::createNew();

        $command = new CheckOutBook($bookId, $userId, -1);
        $book = Book::add($bookId, [], 'title', 'isbn');

        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::once())
            ->method('findById')
            ->with($bookId)
            ->will(self::returnValue($book));

        $repository->expects(self::once())
            ->method('store')
            ->will(self::returnCallback(function (Book $actual) use ($book) {
                self::assertEquals($book->getId(), $actual->getId());
            }));

        $handler = new CheckOutBookHandler($repository);
        $handler->handle($command);
    }
}
