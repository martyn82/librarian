<?php

namespace AppBundle\Collections;

use AppBundle\Collections\Map\EntrySet;

interface Map
{
    /**
     */
    public function clear();

    /**
     * @param mixed $key
     * @return boolean
     */
    public function containsKey($key);

    /**
     * @param mixed $value
     * @return boolean
     */
    public function containsValue($value);

    /**
     * @param mixed $key
     * @return mixed
     */
    public function get($key);

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function put($key, $value);

    /**
     * @param Map $map
     */
    public function putAll(Map $map);

    /**
     * @param mixed $key
     * @return mixed
     */
    public function remove($key);

    /**
     * @return integer
     */
    public function size();

    /**
     * @return EntrySet
     */
    public function entrySet();
}
