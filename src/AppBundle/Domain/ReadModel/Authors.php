<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\Collections\BasicSet;
use AppBundle\Collections\Set;
use AppBundle\Serializing\Serializable;

class Authors implements \IteratorAggregate, Serializable
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

    /**
     * @param array $data
     * @return Authors
     */
    public static function deserialize(array $data)
    {
        assert(array_key_exists('elements', $data));

        return new self(
            array_map(
                function (array $element) {
                    return Author::deserialize($element);
                },
                $data['elements']
            )
        );
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'elements' => array_map(
                function (Author $author) {
                    return $author->serialize();
                },
                $this->innerSet->toArray()
            )
        ];
    }
}
