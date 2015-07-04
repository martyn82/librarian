<?php

namespace AppBundle\Service;

interface CommandHandler
{
    /**
     * @param Command $command
     */
    public function handle(Command $command);
}
