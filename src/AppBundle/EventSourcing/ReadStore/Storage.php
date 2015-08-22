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
     * @param integer $offset
     * @param integer $limit
     * @return Document[]
     */
    public function findAll($offset = 0, $limit = 500);

    /**
     */
    public function clear();

    /**
     * @param array $criteria
     * @param integer $offset
     * @param integer $limit
     * @return Document[]
     */
    public function findBy(array $criteria, $offset = 0, $limit = 500);
}
