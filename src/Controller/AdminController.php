<?php

namespace App\Controller;

use App\Entity\Spot;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(ManagerRegistry $doctrine, Spot $spot = null, Request $request): Response
    {
        $spots = $doctrine->getRepository(Spot::class)->findBy([], ['creationDate'=>'DESC']);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'spots' => $spots
        ]);
    }
    #[Route('/admin/delete/{id}', name: 'deleteSpot_admin')]
    public function deleteSpot(Security $security, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, Spot $spot = null, Request $request): Response
    {

        $user=$security->getUser();

            if ($spot){
                $entityManager->remove($spot);
                $entityManager->flush();

            //Msg flash puis redirect
            $this->addFlash(
                'success',
                'Spot supprimé avec succès!'
            );
                return $this->redirectToRoute('app_spot');
            }
            else{
                $this->addFlash(
                    'error',
                    'Erreur: le spot n\'a pas été supprimée'
                );
                    return $this->redirectToRoute('app_spot');
                }
                return $this->redirectToRoute('app_admin');
        }
}

