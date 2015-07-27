<?php

namespace AppBundle\EventSourcing\MessageHandler;

use AppBundle\EventSourcing\Message\Command;

interface CommandHandler
{
    /**
     * @param Command $command
     */
    public function handle(Command $command);
}
