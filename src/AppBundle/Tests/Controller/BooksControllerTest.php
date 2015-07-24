<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\BooksController;
use AppBundle\Controller\Resource\Book\Author as AuthorResource;
use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Uuid;
use AppBundle\Message\Command;
use AppBundle\MessageBus\CommandBus;
use FOS\RestBundle\View\View;
use FOS\RestBundle\View\ViewHandler;
use Symfony\Component\DependencyInjection\Container;
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
            ->method('getAll')
            ->will(self::returnValue(
                [
                    new Book(Uuid::createNew(), new Authors(), 'title', 0)
                ]
            ));

        $controller = new BooksController($this->service, $this->commandBus);
        $controller->indexAction();
    }

    public function testReadActionReturnsBookResource()
    {
        $id = Uuid::createNew();
        $book = new Book($id, new Authors(), '', 0);

        $controller = new BooksController($this->service, $this->commandBus);
        $resource = $controller->readAction($book);

        self::assertInstanceOf(BookResource::class, $resource);
        self::assertEquals($id, $resource->getId());
    }

    public function testAddBookActionSendsAddBookCommand()
    {
        $this->commandBus->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(
                function (Command $command) {
                    self::assertInstanceOf(AddBook::class, $command);
                }
            ));

        $book = new Book(Uuid::createFromValue(null), new Authors([new Author('first', 'last')]), 'title', -1);
        $resource = BookResource::createFromReadModel($book);

        $this->service->expects(self::once())
            ->method('getBook')
            ->will(self::returnValue($book));

        $controller = new BooksController($this->service, $this->commandBus);
        $controller->addBookAction($resource);
    }

    public function testAddAuthorActionSendsAddAuthorCommand()
    {
        $this->commandBus->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(
                function (Command $command) {
                    self::assertInstanceOf(AddAuthor::class, $command);
                }
            ));

        $id = Uuid::createNew();
        $book = new Book($id, new Authors(), 'title', -1);

        $this->service->expects(self::atLeastOnce())
            ->method('getBook')
            ->will(self::returnValue($book));

        $author = new Author('first', 'last');
        $resource = AuthorResource::createFromReadModel($author);

        $controller = new BooksController($this->service, $this->commandBus);
        $controller->addAuthorAction($id, $resource, -1);
    }
}
