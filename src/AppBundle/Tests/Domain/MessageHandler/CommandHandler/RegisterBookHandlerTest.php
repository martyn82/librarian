<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Message\Command\RegisterBook;
use AppBundle\Domain\MessageHandler\CommandHandler\RegisterBookHandler;
use AppBundle\Domain\Model\Book;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Repository\Books;
use AppBundle\EventStore\Guid;

class RegisterBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterBookHandlerWillCallStoreOnBooksRepository()
    {
        $id = Guid::createNew();
        $title = 'foo';

        $command = new RegisterBook($id, $title);
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
