<?php

namespace AppBundle\Domain\Model;

use AppBundle\Collections\BasicSet;
use AppBundle\Collections\Set;

class Authors implements \IteratorAggregate
{
    /**
     * @var Set
     */
    private $innerSet;

    /**
     * @param AuthorView[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->innerSet = new BasicSet();

        array_walk(
            $elements,
            function (AuthorView $author) {
                $this->add($author);
            }
        );
    }

    /**
     * @param AuthorView $author
     */
    public function add(AuthorView $author)
    {
        $this->innerSet->add($author);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->innerSet->getIterator();
    }
}
