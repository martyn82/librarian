<?php

namespace AppBundle\Collections;

interface Collection
{
    /**
     * @param mixed $element
     * @return boolean
     */
    public function add($element);

    /**
     * @param Collection $elements
     * @return boolean
     */
    public function addAll(Collection $elements);

    /**
     */
    public function clear();

    /**
     * @param mixed $element
     * @return boolean
     */
    public function contains($element);

    /**
     * @param Collection $elements
     * @return boolean
     */
    public function containsAll(Collection $elements);

    /**
     * @return boolean
     */
    public function isEmpty();

    /**
     * @return \Iterator
     */
    public function getIterator();

    /**
     * @param mixed $element
     * @return boolean
     */
    public function remove($element);

    /**
     * @param Collection $elements
     * @return boolean
     */
    public function removeAll(Collection $elements);

    /**
     * @param Collection $elements
     * @return boolean
     */
    public function retainAll(Collection $elements);

    /**
     * @return integer
     */
    public function size();

    /**
     * @return array
     */
    public function toArray();
}
