<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Uuid;
use AppBundle\MessageBus\CommandBus;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
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
     *  "commandBus" = @DI\Inject("librarian.commandbus"),
     *  "serializer" = @DI\Inject("jms_serializer")
     * })
     *
     * @param BookService $bookService
     * @param CommandBus $commandBus
     * @param Serializer $serializer
     */
    public function __construct(BookService $bookService, CommandBus $commandBus, Serializer $serializer)
    {
        $this->bookService = $bookService;
        $this->commandBus = $commandBus;
        $this->serializer = $serializer;
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
     * @return Book
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
     * @Rest\View(statusCode=201)
     *
     * @param Request $request
     * @return Book
     * @throws HttpException
     */
    public function addBookAction(Request $request)
    {
        // request content is a resource, needs to be passed as argument
        $params = $this->serializer->deserialize($request->getContent(), 'array', 'json');

        $id = Uuid::createNew();
        $command = new AddBook($id, $params['title']);
        $this->commandBus->send($command);

        return $this->bookService->getBook($id);
    }

    /**
     * @Rest\Put("/book/{id}",
     *  condition="request.headers.get('content-type') matches '/domain-model=add-author/i'",
     *  requirements={"id"="[a-z0-9]{8}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{4}-[a-z0-9]{12}"},
     *  defaults={"id"=null}
     * )
     * @Rest\View()
     *
     * @param string $id
     * @param Request $request
     * @return Book
     * @throws HttpException
     */
    public function addAuthorAction($id, Request $request)
    {
        // request content is a resource, needs to be passed as argument
        $params = $this->serializer->deserialize($request->getContent(), 'array', 'json');

        $uuid = Uuid::createFromValue($id);

        // version needs to come from request
        $book = $this->bookService->getBook($uuid);
        $command = new AddAuthor($uuid, $params['firstName'], $params['lastName'], $book->getVersion());
        $this->commandBus->send($command);

        return $this->bookService->getBook($uuid);
    }
}
