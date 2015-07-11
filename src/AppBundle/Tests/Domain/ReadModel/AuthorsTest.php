<?php

namespace AppBundle\Tests\Domain\ReadModel;

use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\EventStore\Uuid;

class AuthorsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructAcceptsAuthorViewElements()
    {
        $element = new Author(Uuid::createNew(), 'first', 'last');
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
