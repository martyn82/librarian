<?php

namespace AppBundle\Tests\Controller\Resource;

use AppBundle\Controller\Resource\Book as BookResource;
use AppBundle\Domain\ReadModel\Authors;
use AppBundle\Domain\ReadModel\Book as BookDocument;
use AppBundle\EventSourcing\EventStore\Uuid;

class BookTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateFromReadModelMapsDocumentToResource()
    {
        $id = Uuid::createNew();
        $authors = new Authors();
        $title = 'title';
        $isbn = 'isbn';
        $available = true;
        $version = 1;

        $document = new BookDocument($id, $authors, $title, $isbn, $available, $version);
        $resource = BookResource::createFromReadModel($document);

        self::assertEquals($id, $resource->getId());
        self::assertEquals($title, $resource->getTitle());
        self::assertEquals($isbn, $resource->getISBN());
        self::assertEquals($available, $resource->isAvailable());
    }
}