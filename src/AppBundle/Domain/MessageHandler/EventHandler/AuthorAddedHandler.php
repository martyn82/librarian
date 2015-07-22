<?php

namespace AppBundle\Domain\MessageHandler\EventHandler;

use AppBundle\Domain\Message\Event\AuthorAdded;
use AppBundle\MessageHandler\EventHandler;

interface AuthorAddedHandler extends EventHandler
{
    /**
     * @param AuthorAdded $event
     */
    public function onAuthorAdded(AuthorAdded $event);
}
