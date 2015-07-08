<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\Collections\BasicSet;
use AppBundle\Collections\Set;
use AppBundle\Domain\Storage\Document;

class Authors extends Document implements \IteratorAggregate
{
    /**
     * @var Set
     */
    private $innerSet;

    /**
     * @param Author[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->innerSet = new BasicSet();

        array_walk(
            $elements,
            function (Author $author) {
                $this->add($author);
            }
        );
    }

    /**
     * @param Author $author
     */
    public function add(Author $author)
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
