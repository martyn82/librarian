<?php

namespace AppBundle\Controller;

use AppBundle\Controller\Resource\User as UserResource;
use AppBundle\Controller\View\ViewBuilder;
use AppBundle\Domain\Message\Command\CreateUser;
use AppBundle\Domain\ReadModel\User as UserReadModel;
use AppBundle\Domain\Service\UserService;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\MessageBus\CommandBus;
use JMS\DiExtraBundle\Annotation as DI;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @Rest\Route("users")
 */
class UsersController
{
    /**
     * @var string
     */
    const BASE_ROUTE = '/api/users/';

    /**
     * @var ViewBuilder
     */
    private $viewBuilder;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @DI\InjectParams({
     *  "viewBuilder" = @DI\Inject("view_builder.user"),
     *  "userService" = @DI\Inject("librarian.service.user"),
     *  "commandBus" = @DI\Inject("librarian.commandbus")
     * })
     *
     * @param ViewBuilder $viewBuilder
     * @param UserService $userService
     * @param CommandBus $commandBus
     */
    public function __construct(ViewBuilder $viewBuilder, UserService $userService, CommandBus $commandBus)
    {
        $this->viewBuilder = $viewBuilder;
        $this->userService = $userService;
        $this->commandBus = $commandBus;
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
     * @ParamConverter("user",
     *  class="AppBundle\Domain\ReadModel\User",
     *  options={
     *      "id": "id"
     *  },
     *  converter="param_converter"
     * )
     *
     * @param UserReadModel $user
     * @return View
     * @throws HttpException
     */
    public function readAction(UserReadModel $user)
    {
        return $this->viewBuilder
            ->setDocument($user)
            ->setVersion()
            ->build();
    }

    /**
     * @Rest\Put("",
     *  condition="request.headers.get('content-type') matches '/domain-model=create-user/i'"
     * )
     * @Rest\View()
     *
     * @ParamConverter("userResource",
     *  class="AppBundle\Controller\Resource\User",
     *  converter="fos_rest.request_body"
     * )
     *
     * @param UserResource $userResource
     * @return View
     * @throws HttpException
     */
    public function createAction(UserResource $userResource)
    {
        $user = $this->userService->getUserByUserName($userResource->getUserName());

        if ($user != null) {
            return $this->viewBuilder
                ->setStatus(204)
                ->setVersion($user)
                ->setLocation(static::BASE_ROUTE . $user->getId())
                ->build();
        }

        $id = Uuid::createNew();
        $command = new CreateUser($id, $userResource->getUserName(), $userResource->getEmailAddress());
        $this->commandBus->send($command);

        $user = $this->userService->getUser($id);
        return $this->viewBuilder
            ->setDocument($user)
            ->setVersion()
            ->setLocation(static::BASE_ROUTE . $user->getId())
            ->setStatus(201)
            ->build();
    }
}
