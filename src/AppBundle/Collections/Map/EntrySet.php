<?php

namespace AppBundle\Collections\Map;

use AppBundle\Collections\BasicSet;
use AppBundle\Compare\SimpleComparator;

class EntrySet extends BasicSet
{
    /**
     */
    public function __construct()
    {
        parent::__construct(new SimpleComparator());
    }
}
