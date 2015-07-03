<?php

namespace AppBundle\Domain\Model;

use AppBundle\Collections\Set;

class Authors
{
    /**
     * @var Set
     */
    private $set;

    /**
     * @param Set $set
     */
    public function __construct(Set $set)
    {
        $this->set = $set;
    }

    /**
     * @param Author $author
     * @return bool
     */
    public function add(Author $author)
    {
        return $this->set->add($author);
    }
}
