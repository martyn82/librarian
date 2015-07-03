<?php

namespace AppBundle\Controller;

use AppBundle\EventStore\Guid;
use AppBundle\Service\Command\RegisterBook;
use AppBundle\Service\CommandBus;
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
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @DI\InjectParams({
     *  "commandBus" = @DI\Inject("librarian.commandbus")
     * })
     *
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    /**
     * @Route("/register-book", name="registerBook")
     * @Template("default/index.html.twig")
     *
     * @return array
     */
    public function registerBookAction()
    {
        $command = new RegisterBook(Guid::createNew());
        $this->commandBus->handle($command);

        return [];
    }
}
