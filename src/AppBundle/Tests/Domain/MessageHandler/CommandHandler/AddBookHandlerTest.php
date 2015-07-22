<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\MessageHandler\CommandHandler\AddBookHandler;
use AppBundle\Domain\Model\Author;
use AppBundle\Domain\Model\Book;
use AppBundle\EventStore\Repository;
use AppBundle\EventStore\Uuid;

class AddBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddBookHandlerWillCallStoreOnRepository()
    {
        $id = Uuid::createNew();
        $title = 'foo';
        $authors = [
            Author::create('foo', 'bar')
        ];

        $command = new AddBook($id, $authors, $title);
        $book = Book::add($command->getId(), $command->getAuthors(), $command->getTitle());

        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::once())
            ->method('store')
            ->with($book);

        $handler = new AddBookHandler($repository);
        $handler->handle($command);
    }
}
