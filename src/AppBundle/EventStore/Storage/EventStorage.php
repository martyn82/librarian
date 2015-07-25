<?php

namespace AppBundle\EventStore\Storage;

use AppBundle\EventStore\EventDescriptor;

interface EventStorage
{
    /**
     * @param string $identity
     * @return bool
     */
    public function contains($identity);

    /**
     * @param EventDescriptor $event
     * @return bool
     */
    public function append(EventDescriptor $event);

    /**
     * @param string $identity
     * @return EventDescriptor[]
     */
    public function find($identity);

    /**
     * @return string[]
     */
    public function findIdentities();
}
