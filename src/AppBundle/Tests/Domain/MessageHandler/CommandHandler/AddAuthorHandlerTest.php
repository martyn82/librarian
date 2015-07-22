<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\MessageHandler\CommandHandler\AddAuthorHandler;
use AppBundle\Domain\Model\Author;
use AppBundle\Domain\Model\Book;
use AppBundle\EventStore\Repository;
use AppBundle\EventStore\Uuid;

class AddAuthorHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddAuthorHandlerWillCallStoreOnRepository()
    {
        $bookId = Uuid::createNew();
        $firstName = 'foo';
        $lastName = 'bar';

        $authors = [
            Author::create('first', 'last')
        ];

        $command = new AddAuthor($bookId, $firstName, $lastName, 0);
        $book = Book::add($bookId, $authors, 'title');

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

        $handler = new AddAuthorHandler($repository);
        $handler->handle($command);
    }
}
