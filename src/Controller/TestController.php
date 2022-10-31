<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @Route("/test", name="app_test")
     */
    public function index(): Response
    {
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/route-param/{nom}", name="route_param")
     */
    public function routeParam(String $nom): Response
    {
        $nomRoute = "route parametrée";
        return $this->render(
            'test/route-param.html.twig',
            [
                "nom" => $nom, 
                "route"=> $nomRoute 
            ]
        );
    }
}
