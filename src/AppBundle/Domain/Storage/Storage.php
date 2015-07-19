<?php

namespace AppBundle\Domain\Storage;

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
     * @return Document[]
     */
    public function findAll();
}
