<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\BooksController;
use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Controller\Resource\Book\Author as AuthorResource;
use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\Domain\Service\BookService;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Command;
use AppBundle\EventSourcing\MessageBus\CommandBus;
use Symfony\Component\HttpFoundation\Request;

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
                    new Book(Uuid::createNew(), new Authors(), 'title', 'isbn', 0)
                ]
            ));

        $controller = new BooksController($this->service, $this->commandBus);
        $controller->indexAction(Request::createFromGlobals());
    }

    public function testReadActionReturnsBookResource()
    {
        $id = Uuid::createNew();
        $book = new Book($id, new Authors(), '', '', 0);

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

        $book = new Book(Uuid::createFromValue(null), new Authors([new Author('first', 'last')]), 'title', 'isbn', -1);
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
        $book = new Book($id, new Authors(), 'title', 'isbn', -1);

        $this->service->expects(self::atLeastOnce())
            ->method('getBook')
            ->will(self::returnValue($book));

        $author = new Author('first', 'last');
        $resource = AuthorResource::createFromReadModel($author);

        $controller = new BooksController($this->service, $this->commandBus);
        $controller->addAuthorAction($id, $resource, -1);
    }
}
