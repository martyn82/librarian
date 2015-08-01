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
     * @var integer
     */
    private $version;

    /**
     * @param Uuid $id
     * @param integer $version
     */
    public function __construct(Uuid $id, $version)
    {
        $this->id = $id;
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
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }
}