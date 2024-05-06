<?php

namespace App\Controller\Main;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController
{
    #[Route('/', name: 'main_dashboard', methods: ['GET'])]
    public function index(): Response
    {
        return new Response("<html><h1>Hello, Client dashboard.</h1></html>", Response::HTTP_OK);
    }

}