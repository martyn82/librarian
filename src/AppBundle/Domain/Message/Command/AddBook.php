<?php

namespace AppBundle\Domain\Message\Command;

use AppBundle\Domain\Model\BookView;
use AppBundle\EventStore\Uuid;
use AppBundle\Message\Command;

final class AddBook implements Command
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * @var string
     */
    private $title;

    /**
     * @param Uuid $id
     * @param string $title
     */
    public function __construct(Uuid $id, $title)
    {
        $this->id = $id;
        $this->title = (string) $title;
    }

    /**
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
}
