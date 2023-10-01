<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlaygroundController extends AbstractController
{
    #[Route('/playground', name: 'app_playground')]
    public function index(): Response
    {
        return $this->render('playground/index.html.twig', [
        ]);
    }
    #[Route('/playground_2', name: 'playground_2')]
    public function playground_2(): Response
    {
        return $this->render('playground/playground_2.html.twig', [
        ]);
    }
}
