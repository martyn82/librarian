<?php

namespace AppBundle\Compare;

interface Comparable
{
    /**
     * @param Comparable $comparable
     * @return boolean
     */
    public function equals(Comparable $comparable);
}
