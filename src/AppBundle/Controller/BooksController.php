<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Controller\Resource\Book\Author as AuthorResource;
use AppBundle\Controller\View\ViewBuilder;
use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\ReadModel\Book as BookReadModel;
use AppBundle\Domain\Service\BookService;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\MessageBus\CommandBus;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Rest\Route("/api/books")
 */
class BooksController extends FOSRestController
{
    /**
     * @var ViewBuilder
     */
    private $viewBuilder;

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
     *  "viewBuilder" = @DI\Inject("view_builder"),
     *  "bookService" = @DI\Inject("librarian.service.book"),
     *  "commandBus" = @DI\Inject("librarian.commandbus")
     * })
     *
     * @param ViewBuilder $viewBuilder
     * @param BookService $bookService
     * @param CommandBus $commandBus
     */
    public function __construct(ViewBuilder $viewBuilder, BookService $bookService, CommandBus $commandBus)
    {
        $this->viewBuilder = $viewBuilder;
        $this->bookService = $bookService;
        $this->commandBus = $commandBus;
    }

    /**
     * @Rest\Get("")
     * @Rest\View()
     *
     * @param Request $request
     * @return View
     */
    public function indexAction(Request $request)
    {
        $query = $request->query;

        $offset = (int)$query->get('offset', 0);
        $query->remove('offset');

        $limit = (int)$query->get('limit', 500);
        $query->remove('limit');

        $books = $this->bookService->getAll($query->all(), $offset, $limit);
        return $this->viewBuilder->setDocuments($books)
            ->build();
    }

    /**
     * @Rest\Get("/{id}",
     *  requirements={
     *      "id"="[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}"
     *  },
     *  defaults={
     *      "id"=null
     *  }
     * )
     * @Rest\View()
     *
     * @ParamConverter("id",
     *  class="AppBundle\EventSourcing\EventStore\Uuid",
     *  converter="param_converter"
     * )
     * @ParamConverter("book",
     *  class="AppBundle\Domain\ReadModel\Book",
     *  options={
     *      "id": "id"
     *  },
     *  converter="param_converter"
     * )
     *
     * @param BookReadModel $book
     * @return View
     * @throws HttpException
     */
    public function readAction(BookReadModel $book)
    {
        return $this->viewBuilder->setDocument($book)
            ->setVersion($book)
            ->build();
    }

    /**
     * @Rest\Put("/{id}/author",
     *  condition="request.headers.get('content-type') matches '/domain-model=add-author/i'",
     *  requirements={
     *      "id"="[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}"
     *  },
     *  defaults={
     *      "id"=null
     *  }
     * )
     * @Rest\View()
     *
     * @ParamConverter("id",
     *  class="AppBundle\EventSourcing\EventStore\Uuid",
     *  converter="param_converter"
     * )
     * @ParamConverter("author",
     *  class="AppBundle\Controller\Resource\Book\Author",
     *  converter="fos_rest.request_body"
     * )
     * @ParamConverter("version",
     *  options={
     *      "id": "id"
     *  },
     *  converter="param_converter"
     * )
     *
     * @param Uuid $id
     * @param AuthorResource $author
     * @param integer $version
     * @return View
     * @throws HttpException
     */
    public function addAuthorAction(Uuid $id, AuthorResource $author, $version)
    {
        $command = new AddAuthor($id, $author->getFirstName(), $author->getLastName(), $version);
        $this->commandBus->send($command);

        $updatedBook = $this->bookService->getBook($id);
        return $this->viewBuilder->setDocument($updatedBook)
            ->setVersion($updatedBook)
            ->setLocation('/api/books/' . $updatedBook->getId())
            ->build();
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
     * @return View
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

        $command = new AddBook($id, $authors, $bookResource->getTitle(), $bookResource->getISBN());
        $this->commandBus->send($command);

        $book = $this->bookService->getBook($id);
        return $this->viewBuilder->setDocument($book)
            ->setVersion($book)
            ->setLocation('/api/books/' . $book->getId())
            ->build();
    }
}
