<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\EventSourcing\EventStore\Uuid;
use AppBundle\EventSourcing\Message\Command;

final class CheckOutBook implements Command
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var Uuid
     */
    private $userId;

    /**
     * @var integer
     */
    private $version;

    /**
     * @param Uuid $id
     * @param Uuid $userId
     * @param integer $version
     */
    public function __construct(Uuid $id, Uuid $userId, $version)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->version = (int) $version;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Uuid
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }
}