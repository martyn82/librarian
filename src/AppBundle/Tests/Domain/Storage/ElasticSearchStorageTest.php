<?php

namespace AppBundle\Tests\Domain\Storage;

use AppBundle\Domain\Storage\Document;
use AppBundle\Domain\Storage\ElasticSearchStorage;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class ElasticSearchStorageTest extends \PHPUnit_Framework_TestCase
{
    public function testUpsertAddsDocumentToIndex()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects(self::once())
            ->method('index');

        $document = $this->getMockBuilder(Document::class)
            ->getMock();

        $storage = new ElasticSearchStorage($client, Document::class, 'foo', 'bar');
        $storage->upsert('1', $document);
    }

    public function testDeleteRemovesDocumentFromIndex()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects(self::once())
            ->method('delete');

        $storage = new ElasticSearchStorage($client, Document::class, 'foo', 'bar');
        $storage->delete('1');
    }

    public function testFindRetrievesDocumentFromIndex()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects(self::once())
            ->method('get')
            ->will(self::returnValue(['_source' => []]));

        $storage = new ElasticSearchStorage($client, FakeDocument::class, 'foo', 'bar');
        $storage->find('1');
    }

    public function testFindReturnsNullIfDocumentNotFound()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects(self::once())
            ->method('get')
            ->will(self::throwException(new Missing404Exception()));

        $storage = new ElasticSearchStorage($client, Document::class, 'foo', 'bar');
        self::assertNull($storage->find('1'));
    }

    public function testFindAllReturnsAllDocuments()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects(self::once())
            ->method('search')
            ->will(self::returnValue(['hits' => ['hits' => [['_source' => []]]]]));

        $storage = new ElasticSearchStorage($client, FakeDocument::class, 'foo', 'bar');
        $storage->findAll();
    }

    public function testFindAllReturnsEmptyArrayIfNotFound()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects(self::once())
            ->method('search')
            ->will(self::returnValue([]));

        $storage = new ElasticSearchStorage($client, Document::class, 'foo', 'bar');
        self::assertEmpty($storage->findAll());
    }
}

class FakeDocument extends Document
{
    public static function deserialize(array $data)
    {
        return new self();
    }

    public function serialize()
    {
        return [];
    }
}
