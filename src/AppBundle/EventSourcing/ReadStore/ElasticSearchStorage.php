<?php

namespace AppBundle\EventSourcing\ReadStore;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class ElasticSearchStorage implements Storage
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $documentClass;

    /**
     * @var string
     */
    private $index;

    /**
     * @param Client $client
     * @param string $documentClass
     * @param string $index
     * @param string $type
     */
    public function __construct(Client $client, $documentClass, $index, $type)
    {
        $this->client = $client;
        $this->documentClass = $documentClass;
        $this->index = $index;
        $this->type = $type;
    }

    /**
     * @param string $identity
     * @param Document $document
     */
    public function upsert($identity, Document $document)
    {
        $this->client->index(
            [
                'index' => $this->index,
                'type' => $this->type,
                'id' => $identity,
                'body' => $document->serialize(),
                'refresh' => true
            ]
        );
    }

    /**
     * @param string $identity
     */
    public function delete($identity)
    {
        try {
            $this->client->delete(
                [
                    'index' => $this->index,
                    'type' => $this->type,
                    'id' => $identity,
                    'refresh' => true
                ]
            );
        } catch (Missing404Exception $e) {
            // don't care
        }
    }

    /**
     * @param string $identity
     * @return Document
     */
    public function find($identity)
    {
        try {
            $data = $this->client->get(
                [
                    'index' => $this->index,
                    'type' => $this->type,
                    'id' => $identity
                ]
            );

            $documentClass = $this->documentClass;
            return $documentClass::deserialize($data['_source']);
        } catch (Missing404Exception $e) {
        }

        return null;
    }

    /**
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @return Document[]
     */
    public function findAll(array $filter = [], $offset = 0, $limit = 500)
    {
        $query = [
            'match_all' => []
        ];

        if (!empty($filter)) {
            $query = [
                'filtered' => [
                    'filter' => [
                        'term' => $filter
                    ]
                ]
            ];
        }

        $result = $this->client->search(
            [
                'index' => $this->index,
                'type' => $this->type,
                'body' => [
                    'query' => $query
                ],
                'from' => $offset,
                'size' => $limit
            ]
        );

        if (!array_key_exists('hits', $result)) {
            return [];
        }

        $documentClass = $this->documentClass;

        return array_map(
            function (array $hit) use ($documentClass) {
                return $documentClass::deserialize($hit['_source']);
            },
            $result['hits']['hits']
        );
    }

    /**
     */
    public function clear()
    {
        $indices = $this->client->indices();

        try {
            $indices->delete(['index' => $this->index]);
        } catch (Missing404Exception $e) {
        }

        $indices->create(['index' => $this->index]);
    }
}