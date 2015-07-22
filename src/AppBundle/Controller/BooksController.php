<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Resource\Author as AuthorResource;
use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\Model\Author as AuthorModel;
use AppBundle\Domain\Model\Book as BookModel;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Uuid;
use AppBundle\MessageBus\CommandBus;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Rest\Route("/api")
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
     * @var Serializer
     */
    private $serializer;

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
     * @Rest\Get("/books")
     * @Rest\View()
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->bookService->getAll();
    }

    /**
     * @Rest\Get("/book/{id}",
     *  requirements={"id"="[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}"},
     *  defaults={"id"=null}
     * )
     * @Rest\View()
     *
     * @param string $id
     * @return BookResource
     * @throws HttpException
     */
    public function readAction($id)
    {
        try {
            return $this->bookService->getBook(Uuid::createFromValue($id));
        } catch (ObjectNotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }
    }

    /**
     * @Rest\Post("/book",
     *  condition="request.headers.get('content-type') matches '/domain-model=add-book/i'"
     * )
     * @ParamConverter("book",
     *  class="AppBundle\Controller\Resource\Book",
     *  converter="fos_rest.request_body"
     * )
     * @Rest\View(statusCode=201)
     *
     * @param BookResource $book
     * @return BookResource
     * @throws HttpException
     */
    public function addBookAction(BookResource $book)
    {
        $authors = array_map(
            function (AuthorResource $author) {
                return AuthorModel::create($author->getFirstName(), $author->getLastName());
            },
            $book->getAuthors()
        );

        $id = Uuid::createNew();

        $command = new AddBook($id, $authors, $book->getTitle());
        $this->commandBus->send($command);

        return $this->bookService->getBook($id);
    }

    /**
     * @Rest\Put("/book/{id}/author",
     *  condition="request.headers.get('content-type') matches '/domain-model=add-author/i'",
     *  requirements={"id"="[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}"},
     *  defaults={"id"=null}
     * )
     * @ParamConverter("author",
     *  class="AppBundle\Controller\Resource\Author",
     *  converter="fos_rest.request_body"
     * )
     * @Rest\View()
     *
     * @param string $id
     * @param AuthorResource $author
     * @return BookResource
     * @throws HttpException
     */
    public function addAuthorAction($id, AuthorResource $author)
    {
        $uuid = Uuid::createFromValue($id);

        // version needs to come from request
        $bookReadModel = $this->bookService->getBook($uuid);

        $command = new AddAuthor($uuid, $author->getFirstName(), $author->getLastName(), $bookReadModel->getVersion());
        $this->commandBus->send($command);

        return $this->bookService->getBook($uuid);
    }
}
