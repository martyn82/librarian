<?php

namespace AppBundle\Collections;

class BasicSet extends BasicCollection implements Set
{
    /**
     * @see \AppBundle\Collections\Collection::add()
     */
    public function add($element)
    {
        if ($this->contains($element)) {
            return false;
        }

        return parent::add($element);
    }
}
