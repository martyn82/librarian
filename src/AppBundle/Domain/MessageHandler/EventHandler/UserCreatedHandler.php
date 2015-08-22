<?php

namespace AppBundle\Domain\MessageHandler\EventHandler;

use AppBundle\Domain\Message\Event\UserCreated;
use AppBundle\EventSourcing\MessageHandler\EventHandler;

interface UserCreatedHandler extends EventHandler
{
    /**
     * @param UserCreated $event
     */
    public function onUserCreated(UserCreated $event);
}