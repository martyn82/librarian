<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Uuid;
use AppBundle\MessageBus\CommandBus;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use JMS\DiExtraBundle\Annotation as DI;
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
     * @return Book
     * @throws HttpException
     */
    public function readAction($id)
    {
        try {
            return $this->bookService->getBook(Uuid::deserialize(['value' => $id]));
        } catch (ObjectNotFoundException $e) {
            throw $this->createNotFoundException($e->getMessage(), $e);
        }
    }
}
