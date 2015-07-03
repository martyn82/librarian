<?php

namespace AppBundle\Tests\Service\CommandHandler;

use AppBundle\EventStore\Guid;
use AppBundle\Model\Book;
use AppBundle\Repository\Books;
use AppBundle\Service\Command\RegisterBook;
use AppBundle\Service\CommandHandler\RegisterBookHandler;

class RegisterBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterBookHandlerWillCallStoreOnBooksRepository()
    {
        $id = Guid::createNew();
        $book = Book::register($id);
        $command = new RegisterBook($book);

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
