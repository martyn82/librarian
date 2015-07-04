<?php

namespace AppBundle\MessageHandler;

use AppBundle\Message\Command;

interface CommandHandler
{
    /**
     * @param Command $command
     */
    public function handle(Command $command);
}
