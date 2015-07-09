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
     * @return bool
     */
    public function containsKey($key);

    /**
     * @param mixed $value
     * @return bool
     */
    public function containsValue($value);

    /**
     * @param mixed $key
     * @return mixed
     */
    public function get($key);

    /**
     * @return bool
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
     * @return int
     */
    public function size();

    /**
     * @return EntrySet
     */
    public function entrySet();
}
