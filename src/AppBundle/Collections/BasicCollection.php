<?php

namespace AppBundle\Collections;

class BasicCollection implements Collection
{
    /**
     * @var array
     */
    protected $elements = [];

    /**
     * @see \AppBundle\Collections\Collection::add()
     */
    public function add($element)
    {
        $this->elements[] = $element;
        return true;
    }

    /**
     * @see \AppBundle\Collections\Collection::addAll()
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
     * @see \AppBundle\Collections\Collection::clear()
     */
    public function clear()
    {
        $this->elements = [];
    }

    /**
     * @see \AppBundle\Collections\Collection::contains()
     */
    public function contains($element)
    {
        $index = array_search($element, $this->elements, true);
        return $index !== false;
    }

    /**
     * @see \AppBundle\Collections\Collection::containsAll()
     */
    public function containsAll(Collection $elements)
    {
        $delta = array_diff((array) $elements->toArray(), $this->elements);
        return count($delta) == 0;
    }

    /**
     * @see \AppBundle\Collections\Collection::isEmpty()
     */
    public function isEmpty()
    {
        return $this->size() == 0;
    }

    /**
     * @see \AppBundle\Collections\Collection::getIterator()
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->elements);
    }

    /**
     * @see \AppBundle\Collections\Collection::remove()
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
     * @see \AppBundle\Collections\Collection::removeAll()
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
     * @see \AppBundle\Collections\Collection::retainAll()
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
     * @see \AppBundle\Collections\Collection::size()
     */
    public function size()
    {
        return count($this->elements);
    }

    /**
     * @see \AppBundle\Collections\Collection::toArray()
     */
    public function toArray()
    {
        return $this->elements;
    }
}
