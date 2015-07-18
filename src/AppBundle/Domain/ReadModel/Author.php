<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\Serializing\Serializable;

class Author implements Serializable
{
    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct($firstName, $lastName)
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

    /**
     * @param array $data
     * @return Author
     */
    public static function deserialize(array $data)
    {
        assert(array_key_exists('firstName', $data));
        assert(array_key_exists('lastName', $data));

        return new self($data['firstName'], $data['lastName']);
    }

    /**
     * @return array
     */
    public function serialize()
    {
        return [
            'firstName' => $this->firstName,
            'lastName' => $this->lastName
        ];
    }
}
