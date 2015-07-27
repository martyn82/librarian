<?php

namespace AppBundle\Tests\Domain\ReadModel;

use AppBundle\Compare\Comparable;
use AppBundle\Compare\IncomparableException;
use AppBundle\Domain\ReadModel\Author;

class AuthorTest extends \PHPUnit_Framework_TestCase
{
    public function testSerialization()
    {
        $author = new Author('first', 'last');
        $serialized = $author->serialize();
        $deserialized = Author::deserialize($serialized);

        self::assertEquals($author, $deserialized);
    }

    public function testEqualsReturnsTrueIfBothObjectsHaveTheSameValue()
    {
        $authorA = new Author('first', 'last');
        $authorB = new Author('first', 'last');

        self::assertTrue($authorA->equals($authorB));
        self::assertTrue($authorB->equals($authorA));
    }

    public function testEqualsReturnFalseIfBothObjectsDoNotHaveTheSameValue()
    {
        $authorA = new Author('first', 'last');
        $authorB = new Author('first', 'Last');

        self::assertFalse($authorA->equals($authorB));
        self::assertFalse($authorB->equals($authorA));
    }

    public function testEqualsThrowsExceptionIfBothObjectsAreIncomparable()
    {
        self::setExpectedException(IncomparableException::class);

        $objectA = new Author('first', 'last');
        $objectB = $this->getMockBuilder(Comparable::class)
            ->getMockForAbstractClass();

        $objectA->equals($objectB);
    }
}
