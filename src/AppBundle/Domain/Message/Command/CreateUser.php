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
     * @param Uuid $id
     * @param string $userName
     * @param string $emailAddress
     */
    public function __construct(Uuid $id, $userName, $emailAddress)
    {
        $this->id = $id;
        $this->userName = $userName;
        $this->emailAddress = $emailAddress;
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
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }
}