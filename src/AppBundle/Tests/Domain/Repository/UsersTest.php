<?php

namespace AppBundle\Tests\Domain\Repository;

use AppBundle\Domain\Aggregate\User;
use AppBundle\Domain\Message\Event\UserCreated;
use AppBundle\Domain\Repository\Users;
use AppBundle\EventSourcing\EventStore\EventStore;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Events;

class UsersTest extends \PHPUnit_Framework_TestCase
{
    public function testStoreUserCallsSaveOnStorage()
    {
        $id = Uuid::createNew();
        $userName = 'foo';
        $emailAddress = 'bar';
        $user = User::create($id, $userName, $emailAddress);

        $storage = $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('save')
            ->with($id, $user->getUncommittedChanges(), self::anything());

        $repository = new Users($storage);
        $repository->store($user);
    }

    public function testFindUserByIdLoadsUserFromHistory()
    {
        $userId = Uuid::createNew();

        $userName = 'foo';
        $emailAddress = 'bar';

        $expectedUser = User::create($userId, $userName, $emailAddress);

        $events = new Events(
            [
                new UserCreated($userId, $userName, $emailAddress),
            ]
        );

        $storage = $this->getMockBuilder(EventStore::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('getEventsForAggregate')
            ->with($userId)
            ->will(self::returnValue($events));

        $repository = new Users($storage);
        $actualUser = $repository->findById($userId);

        self::assertEquals($expectedUser->getId(), $actualUser->getId());
    }
}
