<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\BooksController;
use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Controller\Resource\Book\Author as AuthorResource;
use AppBundle\Controller\View\ViewBuilder;
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

    /**
     * @var ViewBuilder
     */
    private $viewBuilder;

    protected function setUp()
    {
        $this->service = $this->getMockBuilder(BookService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewBuilder = new ViewBuilder(BookResource::class);
    }

    public function testIndexActionRetrievesAllBooks()
    {
        $documents = [
            new Book(Uuid::createNew(), new Authors(), 'title', 'isbn', 0)
        ];

        $this->service->expects(self::once())
            ->method('getAll')
            ->will(self::returnValue($documents));

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
        $controller->indexAction(Request::createFromGlobals());
    }

    public function testReadActionReturnsViewWithResource()
    {
        $id = Uuid::createNew();
        $book = new Book($id, new Authors(), '', '', 0);

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
        $view = $controller->readAction($book);

        self::assertInstanceOf(BookResource::class, $view->getData());
        self::assertEquals($id, $view->getData()->getId());
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
        $resource = BookResource::createFromDocument($book);

        $this->service->expects(self::once())
            ->method('getBook')
            ->will(self::returnValue($book));

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
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
        $resource = AuthorResource::createFromDocument($author);

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
        $controller->addAuthorAction($id, $resource, -1);
    }
}
