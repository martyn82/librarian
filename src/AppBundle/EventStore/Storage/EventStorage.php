<?php

namespace AppBundle\EventStore\Storage;

interface EventStorage
{
    /**
     * @param string $id
     * @return bool
     */
    public function contains($id);

    /**
     * @param string $id
     * @param array $data
     * @return bool
     */
    public function append($id, array $data);

    /**
     * @param string $id
     * @return array
     */
    public function find($id);
}
