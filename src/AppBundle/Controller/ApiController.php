<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\AddBook;
use AppBundle\Domain\Service\BookService;
use AppBundle\EventStore\Uuid;
use AppBundle\MessageBus\CommandBus;
use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api")
 */
class ApiController extends Controller
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
     * @Route("/add-book/{authorName}/{title}", name="addBook")
     * @Template("default/index.html.twig")
     *
     * @param string $authorName
     * @param string $title
     * @return array
     */
    public function addBookAction($authorName, $title)
    {
        $bookId = Uuid::createNew();

        $this->commandBus->send(
            new AddBook($bookId, $title)
        );

        $authorNames = explode(' ', $authorName);
        $authorFirstName = $authorNames[0];
        $authorLastName = isset($authorNames[1]) ? $authorNames[1] : '';

        $book = $this->bookService->getBook($bookId);

        $this->commandBus->send(
            new AddAuthor($bookId, $authorFirstName, $authorLastName, $book->getVersion())
        );

        return [
            'book' => $this->bookService->getBook($bookId)
        ];
    }
}
