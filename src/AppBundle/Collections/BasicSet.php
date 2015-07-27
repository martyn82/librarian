<?php

namespace AppBundle\Collections;

use AppBundle\Compare\Comparable;
use AppBundle\Compare\Comparator;

class BasicSet extends BasicCollection implements Set
{
    /**
     * @var Comparator
     */
    private $comparator;

    /**
     * @param Comparator $comparator
     */
    public function __construct(Comparator $comparator)
    {
        $this->comparator = $comparator;
    }

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

    /**
     * @param mixed $element
     * @return boolean
     */
    public function contains($element)
    {
        if (!($element instanceof Comparable)) {
            return parent::contains($element);
        }

        foreach ($this->elements as $el) {
            if ($this->comparator->equals($el, $element)) {
                return true;
            }
        }

        return false;
    }
}
