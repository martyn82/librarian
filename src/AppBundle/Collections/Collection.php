<?php

namespace AppBundle\Collections;

interface Collection
{
    /**
     * @param mixed $element
     * @return bool
     */
    public function add($element);

    /**
     * @param Collection $elements
     * @return bool
     */
    public function addAll(Collection $elements);

    /**
     */
    public function clear();

    /**
     * @param mixed $element
     * @return bool
     */
    public function contains($element);

    /**
     * @param Collection $elements
     * @return bool
     */
    public function containsAll(Collection $elements);

    /**
     * @return bool
     */
    public function isEmpty();

    /**
     * @return \Iterator
     */
    public function getIterator();

    /**
     * @param mixed $element
     * @return bool
     */
    public function remove($element);

    /**
     * @param Collection $elements
     * @return bool
     */
    public function removeAll(Collection $elements);

    /**
     * @param Collection $elements
     * @return bool
     */
    public function retainAll(Collection $elements);

    /**
     * @return int
     */
    public function size();

    /**
     * @return array
     */
    public function toArray();
}
