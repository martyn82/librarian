<?php

namespace AppBundle\Domain\Model;

use AppBundle\Domain\ModelDescriptor\AuthorDescriptor;
use AppBundle\EventStore\Guid;

class AuthorView
{
    use AuthorDescriptor;

    /**
     * @var Guid
     */
    private $id;

    /**
     * @param Guid $id
     * @param string $firstName
     * @param string $lastName
     */
    public function __construct(Guid $id, $firstName, $lastName)
    {
        $this->id = $id;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
