<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Aggregate\Book;
use AppBundle\Domain\Message\Command\ReturnBook;
use AppBundle\Domain\MessageHandler\CommandHandler\ReturnBookHandler;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\EventStore\Uuid;

class ReturnBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnBookHandlerWillCallStoreOnRepository()
    {
        $bookId = Uuid::createNew();

        $command = new ReturnBook($bookId, 0);
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

        $handler = new ReturnBookHandler($repository);
        $handler->handle($command);
    }
}
