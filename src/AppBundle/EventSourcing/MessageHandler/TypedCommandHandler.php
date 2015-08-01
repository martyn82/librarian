<?php

namespace AppBundle\EventSourcing\MessageHandler;

use AppBundle\EventSourcing\Message\Command;

trait TypedCommandHandler
{
    /**
     * @param Command $command
     */
    public function handle(Command $command)
    {
        $commandHandleMethod = $this->inflectHandleMethod($command);
        $this->{$commandHandleMethod}($command);
    }

    /**
     * @param Command $command
     * @return string
     * @throws \InvalidArgumentException
     */
    private function inflectHandleMethod(Command $command)
    {
        $className = get_class($command);
        $classNameParts = explode('\\', $className);
        $commandName = end($classNameParts);
        $commandHandleMethod = 'handle' . $commandName;

        if (!method_exists($this, $commandHandleMethod)) {
            throw new \InvalidArgumentException("Unable to handle command '{$commandName}'.");
        }

        return $commandHandleMethod;
    }
}