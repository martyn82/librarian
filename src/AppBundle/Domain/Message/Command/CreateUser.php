<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Command;

final class CreateUser implements Command
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var string
     */
    private $userName;

    /**
     * @var string
     */
    private $emailAddress;

    /**
     * @var string
     */
    private $fullName;

    /**
     * @param Uuid $id
     * @param string $userName
     * @param string $emailAddress
     * @param string $fullName
     */
    public function __construct(Uuid $id, $userName, $emailAddress, $fullName)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->emailAddress = $emailAddress;
        $this->fullName = $fullName;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }
}