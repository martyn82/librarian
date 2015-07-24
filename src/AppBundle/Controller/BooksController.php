<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Controller\Resource\Book\Author as AuthorResource;
use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\ReadModel\Book;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\ConcurrencyException;
use AppBundle\EventStore\Uuid;
use AppBundle\MessageBus\CommandBus;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

/**
 * @Rest\Route("/api/books")
 */
class BooksController extends FOSRestController
{
    /**
     * @var BookService
     */
    private $bookService;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @DI\InjectParams({
     *  "bookService" = @DI\Inject("librarian.service.book"),
     *  "commandBus" = @DI\Inject("librarian.commandbus")
     * })
     *
     * @param BookService $bookService
     * @param CommandBus $commandBus
     */
    public function __construct(BookService $bookService, CommandBus $commandBus)
    {
        $this->bookService = $bookService;
        $this->commandBus = $commandBus;
    }

    /**
     * @Rest\Get("/")
     * @Rest\View()
     *
     * @return BookResource[]
     */
    public function indexAction()
    {
        return array_map(
            function (Book $book) {
                return BookResource::createFromReadModel($book);
            },
            (array)$this->bookService->getAll()
        );
    }

    /**
     * @Cache(ETag="book.getId() ~ book.getVersion()")
     *
     * @Rest\Get("/{id}",
     *  requirements={
     *      "id"="[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}"
     *  },
     *  defaults={"id"=null}
     * )
     * @Rest\View()
     *
     * @ParamConverter("book",
     *  class="AppBundle\Domain\ReadModel\Book",
     *  converter="param_converter"
     * )
     *
     * @param Book $book
     * @return BookResource
     * @throws HttpException
     */
    public function readAction(Book $book)
    {
        return BookResource::createFromReadModel($book);
    }

    /**
     * @Rest\Put("/{id}/author",
     *  condition="request.headers.get('content-type') matches '/domain-model=add-author/i'",
     *  requirements={
     *      "id"="[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}"
     *  },
     *  defaults={"id"=null}
     * )
     * @Rest\View()
     *
     * @ParamConverter("id",
     *  class="AppBundle\EventStore\Uuid",
     *  converter="param_converter"
     * )
     * @ParamConverter("author",
     *  class="AppBundle\Controller\Resource\Book\Author",
     *  converter="fos_rest.request_body"
     * )
     * @ParamConverter("version",
     *  converter="param_converter"
     * )
     *
     * @param Uuid $id
     * @param AuthorResource $author
     * @param integer $version
     * @return BookResource
     * @throws HttpException
     */
    public function addAuthorAction(Uuid $id, AuthorResource $author, $version)
    {
        $command = new AddAuthor($id, $author->getFirstName(), $author->getLastName(), $version);
        $this->commandBus->send($command);

        $updatedBook = $this->bookService->getBook($id);
        return BookResource::createFromReadModel($updatedBook);
    }

    /**
     * @Rest\Post("",
     *  condition="request.headers.get('content-type') matches '/domain-model=add-book/i'"
     * )
     * @Rest\View(statusCode=201)
     *
     * @ParamConverter("bookResource",
     *  class="AppBundle\Controller\Resource\Book",
     *  converter="fos_rest.request_body"
     * )
     *
     * @param BookResource $bookResource
     * @return BookResource
     * @throws HttpException
     */
    public function addBookAction(BookResource $bookResource)
    {
        $id = Uuid::createNew();

        $authors = array_map(
            function (AuthorResource $author) use ($id) {
                return new AddAuthor($id, $author->getFirstName(), $author->getLastName(), -1);
            },
            $bookResource->getAuthors()
        );

        $command = new AddBook($id, $authors, $bookResource->getTitle());
        $this->commandBus->send($command);

        $book = $this->bookService->getBook($id);
        return BookResource::createFromReadModel($book);
    }
}
