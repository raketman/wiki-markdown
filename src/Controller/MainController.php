<?php

namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * @Route(methods={"GET"}, path="/")
     * @Route(methods={"GET"}, path="{page}", requirements={"page"=".+"})
     */
    public function getIndex()
    {
        return new Response($this->renderView('index.html.twig', [
            'prefix'    => getenv('APP_PREFIX') ? : '',
            'baseUrl'   => getenv('APP_PREFIX') ? : '/',
        ]));
    }

}

