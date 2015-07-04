<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Message\Command\AddAuthor;
use AppBundle\Domain\Message\Command\RegisterBook;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Service\BookService;
use AppBundle\Domain\Service\ObjectNotFoundException;
use AppBundle\EventStore\Guid;
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
     * @DI\InjectParams({
     *  "bookService" = @DI\Inject("librarian.service.book")
     * })
     *
     * @param BookService $bookService
     */
    public function __construct(BookService $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * @Route("/register-book/{authorName}/{title}", name="registerBook")
     * @Template("default/index.html.twig")
     *
     * @param string $authorName
     * @param string $title
     * @return array
     */
    public function registerBookAction($authorName, $title)
    {
        $bookId = Guid::createNew();

        $this->bookService->execute(
            new RegisterBook($bookId, $title)
        );

        $authorId = Guid::createNew();
        $authorNames = explode(' ', $authorName);
        $authorFirstName = $authorNames[0];
        $authorLastName = isset($authorNames[1]) ? $authorNames[1] : '';

        $this->bookService->execute(
            new AddAuthor($authorId, $bookId, $authorFirstName, $authorLastName)
        );

        return [
            'book' => $this->bookService->getBook($bookId)
        ];
    }
}
