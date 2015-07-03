<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\ApiController;
use AppBundle\Service\CommandBus;

class ApiControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterBookCallsHandleOnCommandBus()
    {
        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $controller = new ApiController($commandBus);
        $controller->registerBookAction();
    }
}
