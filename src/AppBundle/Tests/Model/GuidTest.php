<?php

namespace AppBundle\Tests\Model;

use AppBundle\Model\Guid;

class GuidTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateNewWillGenerateAUniqueID()
    {
        $guid = Guid::createNew();

        self::assertNotNull($guid->getValue());
        self::assertNotEmpty($guid->getValue());
        self::assertEquals($guid->getValue(), $guid->__toString());
    }
}
