<?php

namespace AppBundle\Serializing;

interface Serializable
{
    /**
     * @return array
     */
    public function serialize();

    /**
     * @param array $data
     * @return Serializable
     */
    public static function deserialize(array $data);
}