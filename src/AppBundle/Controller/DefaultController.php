<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @return Response
     */
    public function appAction()
    {
        return new Response(
            $this->renderView(
                'default/home.html.twig'
            )
        );
    }
}
