<?php

namespace AppBundle\Compare;

class SimpleComparator implements Comparator
{
    /**
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function equals($a, $b)
    {
        if ($a instanceof Comparable && $b instanceof Comparable) {
            return $a->equals($b);
        }

        return $a === $b;
    }
}
