<?php

namespace AppBundle\Compare;

interface Comparator
{
    /**
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function equals($a, $b);
}
