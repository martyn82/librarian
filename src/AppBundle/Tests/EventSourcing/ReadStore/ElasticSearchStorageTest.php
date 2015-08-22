<?php

namespace AppBundle\Tests\EventSourcing\ReadStore;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\ReadStore\Document;
use AppBundle\EventSourcing\ReadStore\ElasticSearchStorage;
use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;
use Elasticsearch\Namespaces\IndicesNamespace;

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
            ->getMockForAbstractClass();

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

    public function testClearWillClearInternalStorage()
    {
        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $indices = $this->getMockBuilder(IndicesNamespace::class)
            ->disableOriginalConstructor()
            ->getMock();

        $indices->expects(self::once())
            ->method('delete');

        $client->expects(self::atLeastOnce())
            ->method('indices')
            ->will(self::returnValue($indices));

        $storage = new ElasticSearchStorage($client, Document::class, 'foo', 'bar');
        $storage->clear();
    }

    public function testFindByCriteriaWillDoFilterQuery()
    {
        $criteria = [
            'foo' => 'bar'
        ];

        $index = 'foo';
        $type = 'bar';

        $query = [
            'index' => 'foo',
            'type' => 'bar',
            'body' => [
                'query' => [
                    'filtered' => [
                        'query' => [
                            'match_all' => []
                        ],
                        'filter' => [
                            'term' => $criteria
                        ]
                    ]
                ]
            ],
            'from' => 0,
            'size' => 500
        ];

        $client = $this->getMockBuilder(Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client->expects(self::once())
            ->method('search')
            ->with($query)
            ->will(self::returnValue(['hits' => ['hits' => []]]));
        
        $storage = new ElasticSearchStorage($client, Document::class, $index, $type);
        $storage->findBy($criteria);
    }
}

class FakeDocument extends Document
{
    public function getVersion()
    {
        return 0;
    }

    public function getId()
    {
        return Uuid::createFromValue(1);
    }

    public static function deserialize(array $data)
    {
        return new self();
    }

    public function serialize()
    {
        return [];
    }
}
