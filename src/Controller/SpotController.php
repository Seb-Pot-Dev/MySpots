<?php

namespace App\Controller;

use App\Entity\Spot;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SpotController extends AbstractController
{
    #[Route('/spot', name: 'app_spot')]
    public function index(ManagerRegistry $doctrine, Spot $spot = null): Response
    {

        $spots = $doctrine->getRepository(Spot::class)->findBy([], ['name'=>'ASC']);
        return $this->render('spot/index.html.twig', [
            'controller_name' => 'SpotController',
            'spots' => $spots
        ]);
    }
}
