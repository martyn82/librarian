<?php

namespace AppBundle\EventStore\Storage;

interface Storage
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
    public function upsert($id, array $data);

    /**
     * @param string $id
     * @return array
     */
    public function find($id);
}
