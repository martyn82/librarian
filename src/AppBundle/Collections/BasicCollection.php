<?php

namespace AppBundle\Collections;

class BasicCollection implements Collection
{
    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @param mixed $element
     * @return boolean
     */
    public function add($element)
    {
        $this->elements[] = $element;
        return true;
    }

    /**
     * @param Collection $elements
     * @return boolean
     */
    public function addAll(Collection $elements)
    {
        $result = false;

        foreach ($elements->toArray() as $element) {
            $result = $this->add($element) || $result;
        }

        return $result;
    }

    /**
     */
    public function clear()
    {
        $this->elements = [];
    }

    /**
     * @param mixed $element
     * @return boolean
     */
    public function contains($element)
    {
        $index = array_search($element, $this->elements, true);
        return $index !== false;
    }

    /**
     * @param Collection $elements
     * @return boolean
     */
    public function containsAll(Collection $elements)
    {
        $delta = array_diff((array) $elements->toArray(), $this->elements);
        return count($delta) == 0;
    }

    /**
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->size() == 0;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @param mixed $element
     * @return boolean
     */
    public function remove($element)
    {
        $index = array_search($element, $this->elements, true);

        if ($index === false) {
            return false;
        }

        unset($this->elements[$index]);
        $this->elements = array_values($this->elements);
        return true;
    }

    /**
     * @param Collection $elements
     * @return boolean
     */
    public function removeAll(Collection $elements)
    {
        $result = false;

        foreach ($elements->toArray() as $element) {
            $result = $this->remove($element) || $result;
        }

        return $result;
    }

    /**
     * @param Collection $elements
     * @return boolean
     */
    public function retainAll(Collection $elements)
    {
        $intersection = array_intersect($this->elements, (array) $elements->toArray());
        $newElements = array_values($intersection);

        $result = $newElements != $this->elements;
        $this->elements = $newElements;
        return $result;
    }

    /**
     * @return integer
     */
    public function size()
    {
        return count($this->elements);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->elements;
    }
}
