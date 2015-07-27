<?php

namespace AppBundle\Domain\Compare;

use AppBundle\Compare\Comparator;
use AppBundle\Domain\ReadModel\Author;

class AuthorComparator implements Comparator
{
    /**
     * @param mixed $a
     * @param mixed $b
     * @return boolean
     */
    public function equals($a, $b)
    {
        return $this->equalsAuthors($a, $b);
    }

    /**
     * @param Author $a
     * @param Author $b
     * @return boolean
     */
    private function equalsAuthors(Author $a, Author $b)
    {
        return $a->equals($b);
    }
}
