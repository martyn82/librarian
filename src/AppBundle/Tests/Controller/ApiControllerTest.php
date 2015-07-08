<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\ApiController;
use AppBundle\Domain\Service\BookService;
use AppBundle\MessageBus\CommandBus;

class ApiControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddBookCallsHandleOnCommand()
    {
        $service = $this->getMockBuilder(BookService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandBus->expects(self::atLeastOnce())
            ->method('handle');

        $controller = new ApiController($service, $commandBus);
        $controller->addBookAction('foo', 'bar');
    }
}
