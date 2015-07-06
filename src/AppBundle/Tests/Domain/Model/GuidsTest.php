<?php

namespace AppBundle\Tests\Domain\Model;

use AppBundle\Domain\Model\Guids;
use AppBundle\EventStore\Guid;

class GuidsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructAcceptsGuidElements()
    {
        $element = Guid::createNew();
        $guids = new Guids([$element]);

        self::assertCount(1, $guids->getIterator());
    }

    public function testConstructWithNonGuidElementRaisesError()
    {
        self::setExpectedException(\PHPUnit_Framework_Error::class);

        $element = 'foo';
        $guids = new Guids([$element]);
    }
}
