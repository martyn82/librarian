<?php

namespace AppBundle\EventSourcing\Message;

use JMS\Serializer\Annotation as Serializer;

abstract class Event
{
    /**
     * @Serializer\Type("integer")
     * @var integer
     */
    private $version;

    /**
     * @return string
     */
    public static function getName()
    {
        $eventClassParts = explode('\\', static::class);
        return end($eventClassParts);
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return static::getName();
    }

    /**
     * @return integer
     */
    final public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param integer $version
     */
    final public function setVersion($version)
    {
        $this->version = (int) $version;
    }
}
