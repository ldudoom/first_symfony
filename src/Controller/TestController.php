<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        //$this->denyAccessUnlessGranted('ROLE_USER');
        //echo "Hola Mundo !!";
        //return new Response("Hola Mundo !!");
        // return $this->json(['message' => 'Hola Mundo !!', 'status_code' => Response::HTTP_NOT_FOUND],Response::HTTP_NOT_FOUND);
        // return new JsonResponse(['message' => 'Hola Mundo !!', 'status_code' => Response::HTTP_OK],Response::HTTP_OK);
        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }
}
