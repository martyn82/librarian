<?php

namespace AppBundle\Domain\Service;

use AppBundle\Domain\Message\Event\UserCreated;
use AppBundle\Domain\ReadModel\User;
use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Event;
use AppBundle\EventSourcing\ReadStore\Storage;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testHandleWithUserCreatedPropagatesToCorrectHandler()
    {
        $id = Uuid::createNew();
        $userName = 'foo';
        $emailAddress = 'bar';
        $fullName = 'baz boo';

        $event = new UserCreated($id, $userName, $emailAddress, $fullName);

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('upsert')
            ->with($id, self::anything());

        $service = new UserService($storage);
        $service->on($event);
    }

    public function testHandlerForEventUserCreated()
    {
        $id = Uuid::createNew();
        $userName = 'foo';
        $emailAddress = 'bar';
        $fullName = 'baz boo';

        $event = new UserCreated($id, $userName, $emailAddress, $fullName);

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('upsert')
            ->with($id, self::anything());

        $service = new UserService($storage);
        $service->onUserCreated($event);
    }

    public function testGetUserWithNonExistingIdThrowsException()
    {
        self::setExpectedException(ObjectNotFoundException::class);

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new UserService($storage);
        $service->getUser(Uuid::createNew());
    }

    public function testHandleWithUnsupportedEventThrowsException()
    {
        self::setExpectedException(\InvalidArgumentException::class);

        $event = $this->getMockBuilder(Event::class)
           ->getMock();

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $service = new UserService($storage);
        $service->on($event);
    }

    public function testGetUserWithExistingIdReturnsUser()
    {
        $id = Uuid::createNew();
        $user = new User($id, 'foo', 'bar', 'name', 0);

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('find')
            ->will(self::returnValue($user));

        $service = new UserService($storage);
        $actualUser = $service->getUser($id);

        self::assertEquals($user->getId(), $actualUser->getId());
        self::assertEquals($user->getUserName(), $actualUser->getUserName());
        self::assertEquals($user->getEmailAddress(), $actualUser->getEmailAddress());
        self::assertEquals($user->getVersion(), $actualUser->getVersion());
    }

    public function testGetUserByEmailAddressReturnsFoundUser()
    {
        $id = Uuid::createNew();
        $user = new User($id, 'user', 'foo', 'name', 0);

        $storage = $this->getMockBuilder(Storage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $storage->expects(self::once())
            ->method('findBy')
            ->with(['userName' => 'user'], 0, 1)
            ->will(self::returnValue([$user]));

        $service = new UserService($storage);
        $actualUser = $service->getUserByUserName('user');

        self::assertInstanceOf(User::class, $actualUser);
        self::assertEquals($user->getId(), $actualUser->getId());
    }

    public function testGetUserByEmailAddressIfNotFoundReturnsNull()
    {
        $storage = $this->getMockBuilder(Storage::class)
            ->getMockForAbstractClass();

        $service = new UserService($storage);
        $actual = $service->getUserByUserName('foo');

        self::assertNull($actual);
    }
}
