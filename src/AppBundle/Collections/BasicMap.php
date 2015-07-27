<?php

namespace AppBundle\Collections;

use AppBundle\Collections\Map\Entry;
use AppBundle\Collections\Map\EntrySet;

class BasicMap implements Map
{
    /**
     * @var EntrySet
     */
    private $entries;

    /**
     */
    public function __construct()
    {
        $this->entries = new EntrySet();
    }

    /**
     */
    public function clear()
    {
        $this->entries->clear();
    }

    /**
     * @param mixed $key
     * @return boolean
     */
    public function containsKey($key)
    {
        foreach ($this->entries->getIterator() as $entry) {
            /* @var $entry Entry */
            if ($entry->getKey() === $key) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $value
     * @return boolean
     */
    public function containsValue($value)
    {
        foreach ($this->entries->getIterator() as $entry) {
            /* @var $entry Entry */
            if ($entry->getValue() === $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function get($key)
    {
        foreach ($this->entries->getIterator() as $entry) {
            /* @var $entry Entry */
            if ($entry->getKey() === $key) {
                return $entry->getValue();
            }
        }

        return null;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->size() == 0;
    }

    /**
     * @param mixed $key
     * @param mixed $value
     * @return mixed
     */
    public function put($key, $value)
    {
        $previousValue = $this->get($key);

        if ($previousValue !== null) {
            foreach ($this->entries->getIterator() as $entry) {
                /* @var $entry Entry */
                if ($entry->getKey() === $key) {
                    $this->entries->remove($entry);
                    break;
                }
            }
        }

        $this->entries->add(new Entry($key, $value));
        return $previousValue;
    }

    /**
     * @param Map $map
     */
    public function putAll(Map $map)
    {
        foreach ($map->entrySet()->getIterator() as $entry) {
            /* @var $entry Entry */
            $this->put($entry->getKey(), $entry->getValue());
        }
    }

    /**
     * @param mixed $key
     * @return mixed
     */
    public function remove($key)
    {
        foreach ($this->entries->getIterator() as $entry) {
            /* @var $entry Entry */
            if ($entry->getKey() === $key) {
                $value = $entry->getValue();
                $this->entries->remove($entry);
                return $value;
            }
        }

        return null;
    }

    /**
     * @return integer
     */
    public function size()
    {
        return $this->entries->size();
    }

    /**
     * @return EntrySet
     */
    public function entrySet()
    {
        return clone $this->entries;
    }
}
