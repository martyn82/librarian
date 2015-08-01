<?php

namespace AppBundle\Domain\Aggregate;

use AppBundle\EventSourcing\EventStore\Uuid;

class BookUnavailableException extends \Exception
{
    /**
     * @var string
     */
    private static $messageTemplate = "The Book with ID '%s' is unavailable.";

    /**
     * @param Uuid $bookId
     */
    public function __construct(Uuid $bookId)
    {
        parent::__construct(sprintf(static::$messageTemplate, $bookId->getValue()));
    }
}