<?php

namespace AppBundle\Domain\Model;

use AppBundle\Collections\BasicSet;
use AppBundle\Collections\Set;
use AppBundle\EventStore\Guid;

class Guids implements \IteratorAggregate
{
    /**
     * @var Set
     */
    private $innerSet;

    /**
     * @param Guid[] $elements
     */
    public function __construct(array $elements = [])
    {
        $this->innerSet = new BasicSet();

        array_walk(
            $elements,
            function (Guid $id) {
                $this->add($id);
            }
        );
    }

    /**
     * @param Guid $id
     */
    public function add(Guid $id)
    {
        $this->innerSet->add($id);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return $this->innerSet->getIterator();
    }
}
