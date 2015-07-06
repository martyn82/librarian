<?php

namespace AppBundle\Tests\Domain\Model;

use AppBundle\Domain\Model\Authors;
use AppBundle\Domain\Model\AuthorView;
use AppBundle\EventStore\Guid;

class AuthorsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructAcceptsAuthorViewElements()
    {
        $element = new AuthorView(Guid::createNew(), 'first', 'last');
        $authors = new Authors([$element]);

        self::assertCount(1, $authors->getIterator());
    }

    public function testConstructWithNonAuthorViewElementRaisesError()
    {
        self::setExpectedException(\PHPUnit_Framework_Error::class);

        $element = 'foo';
        $authors = new Authors([$element]);
    }
}
