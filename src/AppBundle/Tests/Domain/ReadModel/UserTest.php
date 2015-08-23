<?php

namespace AppBundle\Tests\Domain\ReadModel;

use AppBundle\Domain\ReadModel\User;
use AppBundle\EventSourcing\EventStore\Uuid;

class UserTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $id = Uuid::createNew();
        $user = new User($id, 'foo', 'bar', 'name', 1);
        $serialized = $user->serialize();
        $deserialized = User::deserialize($serialized);

        self::assertEquals($user, $deserialized);
    }
}