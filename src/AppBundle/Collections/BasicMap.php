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
     * @see \AppBundle\Collections\Map::clear()
     */
    public function clear()
    {
        $this->entries->clear();
    }

    /**
     * @see \AppBundle\Collections\Map::containsKey()
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
     * @see \AppBundle\Collections\Map::containsValue()
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
     * @see \AppBundle\Collections\Map::get()
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
     * @see \AppBundle\Collections\Map::isEmpty()
     */
    public function isEmpty()
    {
        return $this->size() == 0;
    }

    /**
     * @see \AppBundle\Collections\Map::put()
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
     * @see \AppBundle\Collections\Map::putAll()
     */
    public function putAll(Map $map)
    {
        foreach ($map->entrySet()->getIterator() as $entry) {
            /* @var $entry Entry */
            $this->put($entry->getKey(), $entry->getValue());
        }
    }

    /**
     * @see \AppBundle\Collections\Map::remove()
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
     * @see \AppBundle\Collections\Map::size()
     */
    public function size()
    {
        return $this->entries->size();
    }

    /**
     * @see \AppBundle\Collections\Map::entrySet()
     */
    public function entrySet()
    {
        return clone $this->entries;
    }
}
