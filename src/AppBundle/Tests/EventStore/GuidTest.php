<?php

namespace AppBundle\Tests\EventStore;

use AppBundle\EventStore\Uuid;

class UuidTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewWillGenerateAUniqueID()
    {
        $Uuid = Uuid::createNew();

        self::assertNotNull($Uuid->getValue());
        self::assertNotEmpty($Uuid->getValue());
        self::assertEquals($Uuid->getValue(), $Uuid->__toString());
    }
}
