<?php

namespace AppBundle\Tests\Domain\MessageHandler\CommandHandler;

use AppBundle\Domain\Aggregate\User;
use AppBundle\Domain\Message\Command\CreateUser;
use AppBundle\Domain\MessageHandler\CommandHandler\CreateUserHandler;
use AppBundle\EventSourcing\EventStore\Repository;
use AppBundle\EventSourcing\EventStore\Uuid;

class CreateUserHandlerTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateUserHandlerWillCallStoreOnRepository()
    {
        $id = Uuid::createNew();
        $userName = 'foo';
        $emailAddress = 'bar';

        $command = new CreateUser($id, $userName, $emailAddress);
        $user = User::create($command->getId(), $command->getUserName(), $command->getEmailAddress());

        $repository = $this->getMockBuilder(Repository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $repository->expects(self::once())
            ->method('store')
            ->with($user);

        $handler = new CreateUserHandler($repository);
        $handler->handle($command);
    }
}
