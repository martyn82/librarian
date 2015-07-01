<?php

namespace AppBundle\Tests\Service\CommandHandler;

use AppBundle\Data\Repository\Books;
use AppBundle\Service\Command\RegisterBook;
use AppBundle\Service\CommandHandler\RegisterBookHandler;
use AppBundle\Model\Book;

class RegisterBookHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterBookHandlerWillCallAddOnBooksRepository()
    {
        $book = new Book();
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
