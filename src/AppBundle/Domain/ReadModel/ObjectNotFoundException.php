<?php

namespace AppBundle\Domain\ReadModel;

use AppBundle\EventStore\Guid;

class ObjectNotFoundException extends \Exception
{
    /**
     * @var string
     */
    private static $messageTemplate = "Object '%s' not found with ID '%s'.";

    /**
     * @param string $objectName
     * @param Guid $objectId
     */
    public function __construct($objectName, Guid $objectId)
    {
        parent::__construct(sprintf(self::$messageTemplate, $objectName, $objectId->getValue()));
    }
}
