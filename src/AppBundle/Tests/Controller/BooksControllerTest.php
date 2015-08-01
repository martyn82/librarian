<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\BooksController;
use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Controller\Resource\Book\Author as AuthorResource;
use AppBundle\Controller\View\ViewBuilder;
use AppBundle\Domain\Aggregate\BookUnavailableException;
use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\Message\Command\CheckOutBook;
use AppBundle\Domain\Message\Command\ReturnBook;
use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\Domain\Service\BookService;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Command;
use AppBundle\EventSourcing\MessageBus\CommandBus;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

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
            new Book(Uuid::createNew(), new Authors(), 'title', 'isbn', true, 0)
        ];

        $this->service->expects(self::once())
            ->method('getAll')
            ->will(self::returnValue($documents));

        $fetcher = $this->getMockBuilder(ParamFetcherInterface::class)
            ->getMockForAbstractClass();

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
        $controller->indexAction($fetcher);
    }

    public function testReadActionReturnsViewWithResource()
    {
        $id = Uuid::createNew();
        $book = new Book($id, new Authors(), '', '', true, 0);

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

        $book = new Book(
            Uuid::createFromValue(null),
            new Authors([new Author('first', 'last')]),
            'title',
            'isbn',
            true,
            -1
        );
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
        $book = new Book($id, new Authors(), 'title', 'isbn', true, -1);

        $this->service->expects(self::atLeastOnce())
            ->method('getBook')
            ->will(self::returnValue($book));

        $author = new Author('first', 'last');
        $resource = AuthorResource::createFromDocument($author);

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
        $controller->addAuthorAction($id, $resource, -1);
    }

    public function testCheckOutBookSendsCheckOutBookCommand()
    {
        $this->commandBus->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(
                function (Command $command) {
                    self::assertInstanceOf(CheckOutBook::class, $command);
                }
            ));

        $id = Uuid::createNew();

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
        $controller->checkOutBookAction($id, 0);
    }

    public function testCheckOutBookThrowsExceptionWhenBookUnavailable()
    {
        self::setExpectedException(PreconditionFailedHttpException::class);
        $id = Uuid::createNew();

        $this->commandBus->expects(self::once())
            ->method('send')
            ->will(self::throwException(new BookUnavailableException($id)));

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
        $controller->checkOutBookAction($id, 0);
    }

    public function testReturnBookSendsReturnBookCommand()
    {
        $this->commandBus->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(
                function (Command $command) {
                    self::assertInstanceOf(ReturnBook::class, $command);
                }
            ));

        $id = Uuid::createNew();

        $controller = new BooksController($this->viewBuilder, $this->service, $this->commandBus);
        $controller->returnBookAction($id, 0);
    }
}
