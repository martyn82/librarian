<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Service\BookService;
use AppBundle\MessageBus\CommandBus;
use FOS\RestBundle\Controller\Annotations as REST;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\DiExtraBundle\Annotation as DI;

/**
 * @REST\Route("/api")
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
     * @REST\Get("/books")
     * @REST\View()
     *
     * @return array
     */
    public function indexAction()
    {
        return $this->bookService->getAll();
    }
}
