<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Repository\Books;
use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\EventStore\Guid;
use AppBundle\Domain\Model\Book;
use AppBundle\Domain\MessageHandler\CommandHandler\AddAuthorHandler;
class AddAuthorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAuthorHandlerWillCallStoreOnBooksRepository()
    {
        $id = Guid::createNew();
        $bookId = Guid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $command = new AddAuthor($id, $bookId, $firstName, $lastName);
        $book = Book::register($bookId, 'title');

        $repository = $this->getMockBuilder(Books::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::once())
            ->method('store')
            ->will(self::returnCallback(function (Book $actual) use ($book) {
                self::assertEquals($book->getId(), $actual->getId());
            }));

        $handler = new AddAuthorHandler($repository);
        $handler->handle($command);
    }
}
