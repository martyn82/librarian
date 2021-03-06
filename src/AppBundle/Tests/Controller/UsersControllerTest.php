<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\Resource\User as UserResource;
use AppBundle\Controller\UsersController;
use AppBundle\Controller\View\ViewBuilder;
use AppBundle\Domain\Message\Command\CreateUser;
use AppBundle\Domain\ReadModel\User;
use AppBundle\Domain\Service\UserService;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Command;
use AppBundle\EventSourcing\MessageBus\CommandBus;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UsersControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserService
     */
    private $service;

    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var ViewBuilder
     */
    private $viewBuilder;

    protected function setUp()
    {
        $this->service = $this->getMockBuilder(UserService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->viewBuilder = new ViewBuilder(UserResource::class);
    }

    public function testReadActionReturnsViewWithResource()
    {
        $id = Uuid::createNew();
        $user = new User($id, 'user', 'email', 'name', 0);

        $controller = new UsersController($this->viewBuilder, $this->service, $this->commandBus);
        $view = $controller->readAction($user);

        self::assertInstanceOf(UserResource::class, $view->getData());
        self::assertEquals($id, $view->getData()->getId());
    }

    public function testCreateActionReturnsNoContentIfUserAlreadyExists()
    {
        $id = Uuid::createNew();
        $user = new User($id, 'user', 'email', 'name', 0);

        $resource = $this->getMockBuilder(UserResource::class)
            ->disableOriginalConstructor()
            ->getMock();

        $resource->expects(self::atLeastOnce())
            ->method('getUserName')
            ->will(self::returnValue('user'));

        $this->service->expects(self::once())
            ->method('getUserByUserName')
            ->with('user')
            ->will(self::returnValue($user));

        $controller = new UsersController($this->viewBuilder, $this->service, $this->commandBus);
        $view = $controller->createAction($resource);

        self::assertEquals(204, $view->getStatusCode());
    }

    public function testCreateActionCreatesUserIfNotExists()
    {
        $id = Uuid::createNew();
        $user = new User($id, 'user', 'email', 'name', 0);
        $resource = UserResource::createFromReadModel($user);

        $this->commandBus->expects(self::once())
            ->method('send')
            ->will(self::returnCallback(function (Command $command) {
                self::assertInstanceOf(CreateUser::class, $command);
            }));

        $this->service->expects(self::once())
            ->method('getUser')
            ->will(self::returnValue($user));

        $controller = new UsersController($this->viewBuilder, $this->service, $this->commandBus);
        $view = $controller->createAction($resource);

        self::assertEquals(201, $view->getStatusCode());
    }

    public function testGetByUserNameActionRetrievesUserByUserName()
    {
        $id = Uuid::createNew();
        $user = new User($id, 'user', 'email', 'name', 0);

        $this->service->expects(self::once())
            ->method('getUserByUserName')
            ->with('user')
            ->will(self::returnValue($user));

        $fetcher = $this->getMockBuilder(ParamFetcherInterface::class)
            ->getMockForAbstractClass();

        $fetcher->expects(self::once())
            ->method('get')
            ->with('user_name')
            ->will(self::returnValue('user'));

        $controller = new UsersController($this->viewBuilder, $this->service, $this->commandBus);
        $view = $controller->getByUserNameAction($fetcher);

        self::assertEquals(200, $view->getStatusCode());
    }

    public function testGetByUserNameActionThrowsNotFoundExceptionIfNotFound()
    {
        self::setExpectedException(NotFoundHttpException::class);

        $id = Uuid::createNew();
        $user = new User($id, 'user', 'email', 'name', 0);

        $this->service->expects(self::once())
            ->method('getUserByUserName')
            ->with('user')
            ->will(self::returnValue(null));

        $fetcher = $this->getMockBuilder(ParamFetcherInterface::class)
            ->getMockForAbstractClass();

        $fetcher->expects(self::once())
            ->method('get')
            ->with('user_name')
            ->will(self::returnValue('user'));

        $controller = new UsersController($this->viewBuilder, $this->service, $this->commandBus);
        $view = $controller->getByUserNameAction($fetcher);
    }
}