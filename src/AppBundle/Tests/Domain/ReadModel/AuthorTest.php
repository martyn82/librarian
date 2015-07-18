<?php

namespace AppBundle\Tests\Domain\ReadModel;

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
}