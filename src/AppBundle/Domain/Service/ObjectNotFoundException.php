<?php

namespace AppBundle\Domain\Service;

use AppBundle\EventStore\Uuid;

class ObjectNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private static $messageTemplate = "Object '%s' not found with ID '%s'.";

    /**
     * @param string $objectName
     * @param Uuid $objectId
     */
    public function __construct($objectName, Uuid $objectId)
    {
        parent::__construct(sprintf(self::$messageTemplate, $objectName, $objectId->getValue()));
    }
}
