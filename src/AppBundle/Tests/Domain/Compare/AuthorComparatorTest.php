<?php

namespace AppBundle\Tests\Domain\Compare;

use AppBundle\Domain\Compare\AuthorComparator;
use AppBundle\Domain\ReadModel\Author;

class AuthorComparatorTest extends \PHPUnit_Framework_TestCase
{
    public function testEqualsReturnsTrueIfTwoAuthorsAreEqual()
    {
        $author1 = new Author('first', 'last');
        $author2 = new Author('first', 'last');

        $comparator = new AuthorComparator();
        self::assertTrue($comparator->equals($author1, $author2));
    }

    public function testEqualsReturnsFalseIfTwoAuthorsAreNotEqual()
    {
        $author1 = new Author('first', 'last');
        $author2 = new Author('last', 'first');

        $comparator = new AuthorComparator();
        self::assertFalse($comparator->equals($author1, $author2));
    }

    public function testEqualsThrowsExceptionIfNotGivenAuthors()
    {
        self::setExpectedException(\PHPUnit_Framework_Error::class);

        $obj1 = new Author('first', 'last');
        $obj2 = new \stdClass();

        $comparator = new AuthorComparator();
        self::assertTrue($comparator->equals($obj1, $obj2));
    }
}