<?php

namespace AppBundle\EventSourcing\ReadStore;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Serializing\Serializable;

abstract class Document implements Serializable
{
    /**
     * @return Uuid
     */
    abstract public function getId();

    /**
     * @return integer
     */
    abstract public function getVersion();
}
