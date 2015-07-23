<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Controller\Resource\Book\Author as AuthorResource;
use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
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
     * @return BookResource[]
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
        $id = Uuid::createNew();

        $authors = array_map(
            function (AuthorResource $author) use ($id) {
                return new AddAuthor($id, $author->getFirstName(), $author->getLastName(), -1);
            },
            $book->getAuthors()
        );

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
     * @ParamConverter("id",
     *  class="AppBundle\EventStore/Uuid",
     *  converter="fos_rest.request_param"
     * )
     * @ParamConverter("author",
     *  class="AppBundle\Controller\Resource\Book\Author",
     *  converter="fos_rest.request_body"
     * )
     * @Rest\View()
     *
     * @param Uuid $id
     * @param AuthorResource $author
     * @return BookResource
     * @throws HttpException
     */
    public function addAuthorAction(Uuid $id, AuthorResource $author)
    {
        // version needs to come from request
        $bookReadModel = $this->bookService->getBook($id);

        $command = new AddAuthor($id, $author->getFirstName(), $author->getLastName(), $bookReadModel->getVersion());
        $this->commandBus->send($command);

        return $this->bookService->getBook($id);
    }
}
