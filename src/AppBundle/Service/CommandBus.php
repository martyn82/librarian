<?php

namespace AppBundle\Service;

class CommandBus
{
    /**
     * @var array
     */
    private $commandHandlerMap;

    /**
     * @param array $commandHandlerMap
     */
    public function __construct(array $commandHandlerMap)
    {
        $this->commandHandlerMap = $commandHandlerMap;
    }

    /**
     * @param Command $command
     * @throws NoCommandHandlerException
     */
    public function handle(Command $command)
    {
        $commandClassName = get_class($command);

        if (!isset($this->commandHandlerMap[$commandClassName])) {
            throw new NoCommandHandlerException($commandClassName);
        }

        $handler = $this->commandHandlerMap[$commandClassName];
        $handler->handle($command);
    }
}
