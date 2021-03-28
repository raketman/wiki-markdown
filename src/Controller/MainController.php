<?php

namespace App\Controller;



use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * @Route(methods={"GET"}, path="/")
     */
    public function getIndex()
    {
        return new Response($this->renderView('index.html.twig'));
    }

}

