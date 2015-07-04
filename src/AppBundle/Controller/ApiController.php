<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Message\Command\RegisterBook;
use AppBundle\Domain\Model\BookView;
use AppBundle\Domain\Service\ReadModel;
use AppBundle\EventStore\Guid;
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
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var ReadModel
     */
    private $readModel;

    /**
     * @DI\InjectParams({
     *  "commandBus" = @DI\Inject("librarian.commandbus"),
     *  "readModel" = @DI\Inject("librarian.readmodel.service")
     * })
     *
     * @param CommandBus $commandBus
     * @param ReadModel $readModel
     */
    public function __construct(CommandBus $commandBus, ReadModel $readModel)
    {
        $this->commandBus = $commandBus;
        $this->readModel = $readModel;
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

        $command = new RegisterBook($id, $title);
        $this->commandBus->handle($command);

        return [
            'book' => $this->readModel->getBook($id)
        ];
    }
}
