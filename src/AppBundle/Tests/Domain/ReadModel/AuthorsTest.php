<?php

namespace AppBundle\Tests\Domain\ReadModel;

use AppBundle\Domain\ReadModel\Author;
use AppBundle\Domain\ReadModel\Authors;

class AuthorsTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructAcceptsAuthorViewElements()
    {
        $element = new Author('first', 'last');
        $authors = new Authors([$element]);

        self::assertCount(1, $authors->getIterator());
    }

    public function testConstructWithNonAuthorViewElementRaisesError()
    {
        self::setExpectedException(\PHPUnit_Framework_Error::class);

        $element = 'foo';
        $authors = new Authors([$element]);
    }

    public function testSerialization()
    {
        $authors = new Authors([new Author('first', 'last')]);
        $serialized = $authors->serialize();
        $deserialized = Authors::deserialize($serialized);

        self::assertEquals($authors, $deserialized);
    }
}
