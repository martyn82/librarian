<?php

namespace AppBundle\Controller\Resource;

use AppBundle\EventSourcing\ReadStore\ReadModel;

interface Resource
{
    /**
     * @param ReadModel $document
     * @return Resource
     */
    public static function createFromReadModel(ReadModel $document);
}
