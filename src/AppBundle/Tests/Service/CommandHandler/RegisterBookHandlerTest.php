<?php

namespace AppBundle\Tests\Service\CommandHandler;

use AppBundle\Repository\Books;
use AppBundle\Model\Book;
use AppBundle\Model\Guid;
use AppBundle\Service\Command\RegisterBook;
use AppBundle\Service\CommandHandler\RegisterBookHandler;

class RegisterBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterBookHandlerWillCallAddOnBooksRepository()
    {
        $id = Guid::createNew();

        $book = new Book($id);
        $command = new RegisterBook($book);

        $repository = $this->getMockBuilder(Books::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::once())
            ->method('add')
            ->with($book);

        $handler = new RegisterBookHandler($repository);
        $handler->handle($command);
    }
}
