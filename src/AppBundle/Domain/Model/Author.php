<?php

namespace AppBundle\Domain\Model;

use JMS\Serializer\Annotation as Serializer;

class Author
{
    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $firstName;

    /**
     * @Serializer\Type("string")
     * @var string
     */
    private $lastName;

    /**
     * @param string $firstName
     * @param string $lastName
     * @return Author
     */
    public static function create($firstName, $lastName)
    {
        return new self($firstName, $lastName);
    }

    /**
     * @param string $firstName
     * @param string $lastName
     */
    private function __construct($firstName, $lastName)
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

    /**
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}