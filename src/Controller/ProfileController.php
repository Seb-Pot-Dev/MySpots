<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfileController extends AbstractController
{
    #[Route('/profile/my_profile', name: 'my_profile')]
    public function index(Security $security, ManagerRegistry $doctrine, User $user = null, Request $request): Response
    {
        $user = $this->getUser();

        if(!$user){
            throw new \Exception('Accès refusé');
            redirectToRoute('/home/index.html.twig');
        }
    
        return $this->render('profile/index.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }
    #[Route('/profile/my_infos', name: 'my_infos')]
    public function my_infos(Security $security, ManagerRegistry $doctrine, User $user = null, Request $request): Response
    {
        $user = $this->getUser();

        if(!$user){
            throw new \Exception('Accès refusé');
            redirectToRoute('/home/index.html.twig');
        }
    
        return $this->render('profile/my_infos.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }
    #[Route('/profile/my_favorites', name: 'my_favorites')]
    public function my_favorite(Security $security, ManagerRegistry $doctrine, User $user = null, Request $request): Response
    {
        $user = $this->getUser();
        
        if(!$user){
            throw new \Exception('Accès refusé');
            redirectToRoute('/home/index.html.twig');
        }
        
        return $this->render('profile/my_favorites.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }
    #[Route('/profile/my_spots', name: 'my_spots')]
    public function my_spots(Security $security, ManagerRegistry $doctrine, User $user = null, Request $request): Response
    {
        $user = $this->getUser();
        
        if(!$user){
            throw new \Exception('Accès refusé');
            redirectToRoute('/home/index.html.twig');
        }
        
        return $this->render('profile/my_spots.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
        ]);
    }
}

