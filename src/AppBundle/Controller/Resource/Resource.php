<?php

namespace AppBundle\Controller\Resource;

use AppBundle\EventSourcing\ReadStore\Document;

abstract class Resource
{
    /**
     * @param Document $document
     * @return Resource
     */
    abstract public static function createFromDocument(Document $document);
}
