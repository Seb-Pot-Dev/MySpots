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

        $tab = [];
        foreach($spots as $spot){
            $tab[] = [
                $spot->getName() => [
                    $spot->getLat(), 
                    $spot->getLng(),
                    $spot->getDescription()
                ]
            ];
        }

        $tabCoords = json_encode($tab, JSON_HEX_APOS);

        return $this->render('spot/index.html.twig', [
            'controller_name' => 'SpotController',
            'spots' => $tabCoords,
            'spotsList' => $spots
        ]);
    }
    #[Route('/spot/{id}', name: 'show_spot')]
    public function show(Spot $spot = null): Response
    //On appel l'objet Spot dont l'id est passé en parametre par la route
    {        
        if ($spot){

            return $this->render('spot/show.html.twig', [
                'controller_name' => 'SpotController',
                'spot' => $spot
            ]);
        }
    }
}
