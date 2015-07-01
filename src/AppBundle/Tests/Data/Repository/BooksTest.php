<?php

namespace AppBundle\Tests\Data\Repository;

use AppBundle\Model\Book;
use Doctrine\ODM\MongoDB\DocumentManager;
use AppBundle\Data\Repository\Books;

class BooksTest extends \PHPUnit_Framework_TestCase
{
    public function testAddBookCallsPersistOnDocumentManager()
    {
        $book = new Book();

        $manager = $this->getMockBuilder(DocumentManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $manager->expects(self::once())
            ->method('persist')
            ->with($book);

        $repository = new Books($manager);
        $repository->add($book);
    }
}
