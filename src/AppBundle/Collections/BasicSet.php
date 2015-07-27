<?php

namespace AppBundle\Collections;

class BasicSet extends BasicCollection implements Set
{
    /**
     * @return boolean
     */
    public function add($element)
    {
        if ($this->contains($element)) {
            return false;
        }

        return parent::add($element);
    }
}
