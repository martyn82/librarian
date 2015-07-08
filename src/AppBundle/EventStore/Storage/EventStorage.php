<?php

namespace AppBundle\EventStore\Storage;

interface EventStorage
{
    /**
     * @param string $identity
     * @return bool
     */
    public function contains($identity);

    /**
     * @param string $identity
     * @param array $data
     * @return bool
     */
    public function append($identity, array $data);

    /**
     * @param string $identity
     * @return array
     */
    public function find($identity);
}
