<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Controller\AppController;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class AppControllerTest extends \PHPUnit_Framework_TestCase
{
    public function testAppActionRendersView()
    {
        $templating = $this->getMockBuilder(EngineInterface::class)
            ->getMock();

        $templating->expects(self::once())
            ->method('render');

        $container = $this->getMockBuilder(Container::class)
            ->getMock();

        $container->expects(self::atLeastOnce())
            ->method('get')
            ->will(self::returnCallback(function ($name) use ($templating) {
                if ($name == 'templating') {
                    return $templating;
                }
            }));

        $controller = new AppController();
        $controller->setContainer($container);

        $response = $controller->appAction();
        self::assertInstanceOf(Response::class, $response);
    }
}
