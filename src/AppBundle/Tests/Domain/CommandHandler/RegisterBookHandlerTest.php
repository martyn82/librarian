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
        $command = new RegisterBook(Guid::createNew(), 'foo');
        $book = Book::register($command->getId(), $command->getTitle());

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
