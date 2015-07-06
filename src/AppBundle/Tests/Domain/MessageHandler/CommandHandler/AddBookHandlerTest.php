<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\MessageHandler\CommandHandler\AddBookHandler;
use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Repository\Books;
use AppBundle\EventStore\Guid;

class AddBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddBookHandlerWillCallStoreOnBooksRepository()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $command = new AddBook($id, $title);
        $book = Book::add($command->getId(), $command->getTitle());

        $repository = $this->getMockBuilder(Books::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::once())
            ->method('store')
            ->with($book);

        $handler = new AddBookHandler($repository);
        $handler->handle($command);
    }
}
