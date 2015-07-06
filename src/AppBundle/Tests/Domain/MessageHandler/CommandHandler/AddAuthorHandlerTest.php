<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\MessageHandler\CommandHandler\AddAuthorHandler;
use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Repository\Books;
use AppBundle\EventStore\Guid;

class AddAuthorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAuthorHandlerWillCallStoreOnBooksRepository()
    {
        $authorId = Guid::createNew();
        $bookId = Guid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $command = new AddAuthor($authorId, $bookId, $firstName, $lastName);
        $book = Book::add($bookId, 'title');

        $repository = $this->getMockBuilder(Books::class)
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

        $handler = new AddAuthorHandler($repository);
        $handler->handle($command);
    }
}
