<?php

namespace AppBundle\EventSourcing\ReadStore;

interface Storage
{
    /**
     * @param string $identity
     * @param Document $document
     */
    public function upsert($identity, Document $document);

    /**
     * @param string $identity
     */
    public function delete($identity);

    /**
     * @param string $identity
     * @return Document
     */
    public function find($identity);

    /**
     * @param array $filter
     * @param int $offset
     * @param int $limit
     * @return Document[]
     */
    public function findAll(array $filter = [], $offset = 0, $limit = 500);

    /**
     */
    public function clear();
}
