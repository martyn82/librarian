<?php

namespace AppBundle\Controller\Resource\Book;

use AppBundle\Controller\Resource\Resource;
use AppBundle\EventSourcing\ReadStore\ReadModel;
use JMS\Serializer\Annotation as Serializer;

class Author implements Resource
{
    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("first_name")
     * @var string
     */
    private $firstName;

    /**
     * @Serializer\Type("string")
     * @Serializer\SerializedName("last_name")
     * @var string
     */
    private $lastName;

    /**
     * @param ReadModel $author
     * @return Author
     */
    public static function createFromReadModel(ReadModel $author)
    {
        return new self($author->getFirstName(), $author->getLastName());
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