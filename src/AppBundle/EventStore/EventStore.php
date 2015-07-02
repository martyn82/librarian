<?php

namespace AppBundle\EventStore;

use AppBundle\Model\Guid;

interface EventStore
{
    /**
     * @param Guid $id
     * @param Events $events
     */
    public function save(Guid $id, Events $events);
}
