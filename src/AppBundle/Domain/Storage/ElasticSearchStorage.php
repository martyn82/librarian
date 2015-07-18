<?php

namespace AppBundle\Domain\Storage;

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
}