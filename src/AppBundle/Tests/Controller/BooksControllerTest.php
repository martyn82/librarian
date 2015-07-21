<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\BooksController;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Uuid;
use AppBundle\MessageBus\CommandBus;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BooksControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var BookService
     */
    private $service;

    /**
     * @var CommandBus
     */
    private $commandBus;

    protected function setUp()
    {
        $this->service = $this->getMockBuilder(BookService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testIndexActionRetrievesAllBooks()
    {
        $this->service->expects(self::once())
            ->method('getAll');

        $controller = new BooksController($this->service, $this->commandBus);
        $controller->indexAction();
    }

    public function testReadActionRetrievesBookById()
    {
        $id = Uuid::createNew();

        $this->service->expects(self::once())
            ->method('getBook')
            ->with($id);

        $controller = new BooksController($this->service, $this->commandBus);
        $controller->readAction($id);
    }

    public function testReadActionThrowsNotFoundExceptionIfNotFound()
    {
        self::setExpectedException(NotFoundHttpException::class);

        $id = Uuid::createNew();

        $this->service->expects(self::once())
            ->method('getBook')
            ->with($id)
            ->will(self::throwException(new ObjectNotFoundException('Book', $id)));

        $controller = new BooksController($this->service, $this->commandBus);
        $controller->readAction($id);
    }
}
