<?php

namespace AppBundle\Model;

class Guid
{
    /**
     * @var string
     */
    private $value;

    /**
     * @return Guid
     */
    public static function createNew()
    {
        return new self(\uniqid('', true));
    }

    /**
     * @param string $value
     */
    private function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getValue();
    }
}
