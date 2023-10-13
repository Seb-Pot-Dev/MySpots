<?php

namespace App\Controller;

use App\Entity\Module;
use App\Entity\Spot;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ManagerRegistry $doctrine, Spot $spot = null, Module $module = null): Response
    {
        // récupérer la liste de tous les users
        $users = $doctrine->getRepository(User::class)->findAll();
        //récupérer la liste de tout les spots
        $spots = $doctrine->getRepository(Spot::class)->findValidatedSpots();
        //récupérer la liste de tout les modules
        $modules = $doctrine->getRepository(Module::class)->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'spotsList' => $spots,
            'modules' => $modules,
            'users' => $users
        ]);
    }
}