<?php

namespace AppBundle\Domain\Message\Event;

use AppBundle\EventStore\Uuid;
use AppBundle\Message\Event;
use JMS\Serializer\Annotation as Serializer;

final class BookAdded extends Event
{
    /**
     * @Serializer\Type("AppBundle\EventStore\Uuid")
     * @var Uuid
     */
    private $id;

    /**
     * @Serializer\Type("string")
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
        $this->title = $title;
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
