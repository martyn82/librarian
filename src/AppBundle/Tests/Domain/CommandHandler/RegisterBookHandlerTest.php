<?php

namespace AppBundle\Tests\Domain\CommandHandler;

use AppBundle\Domain\Command\RegisterBook;
use AppBundle\Domain\CommandHandler\RegisterBookHandler;
use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Repository\Books;
use AppBundle\EventStore\Guid;

class RegisterBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterBookHandlerWillCallStoreOnBooksRepository()
    {
        $book = Book::register(Guid::createNew());
        $command = new RegisterBook($book->getId());

        $repository = $this->getMockBuilder(Books::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::once())
            ->method('store')
            ->with($book);

        $handler = new RegisterBookHandler($repository);
        $handler->handle($command);
    }
}
