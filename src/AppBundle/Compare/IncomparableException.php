<?php

namespace AppBundle\Compare;

class IncomparableException extends \InvalidArgumentException
{
    /**
     * @var string
     */
    private static $messageTemplate = "Object of type %s cannot be compared to an object of type %s.";

    /**
     * @param object $object1
     * @param object $object2
     */
    public function __construct($object1, $object2)
    {
        parent::__construct(sprintf(static::$messageTemplate, get_class($object1), get_class($object2)));
    }
}
