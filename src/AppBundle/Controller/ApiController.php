<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Message\Command\RegisterBook;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Service\BookService;
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
     * @Route("/register-book/{title}", name="registerBook")
     * @Template("default/index.html.twig")
     *
     * @param string $title
     * @return array
     */
    public function registerBookAction($title)
    {
        $id = Guid::createNew();

        $this->bookService->execute(
            new RegisterBook($id, $title)
        );

        return [
            'book' => $this->bookService->getBook($id)
        ];
    }
}
