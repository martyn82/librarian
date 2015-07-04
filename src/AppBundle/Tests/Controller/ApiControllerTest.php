<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\ApiController;
use AppBundle\Domain\Service\ReadModel;
use AppBundle\Service\CommandBus;

class ApiControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testRegisterBookCallsHandleOnCommandBus()
    {
        $commandBus = $this->getMockBuilder(CommandBus::class)
            ->disableOriginalConstructor()
            ->getMock();

        $readModel = $this->getMockBuilder(ReadModel::class)
            ->getMock();

        $controller = new ApiController($commandBus, $readModel);
        $controller->registerBookAction('foo');
    }
}
