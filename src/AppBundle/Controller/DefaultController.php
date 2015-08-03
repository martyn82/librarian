<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @param string $page
     * @return Response
     */
    public function appAction($page = null)
    {
        return new Response(
            $this->renderView(
                'base.html.twig'
            )
        );
    }
}
