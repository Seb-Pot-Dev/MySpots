<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\User;
use App\Entity\Module;
use App\Entity\Picture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminController extends AbstractController
{
   
    //*******************************************************CRUD SPOT*********************************************************** */
    #[Route('/admin/deleteSpot/{id}', name: 'deleteSpot_admin')]
    public function deleteSpot(Security $security, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, Spot $spot = null, Request $request): Response
    {
        //RESTE A GERER LES CONDITIONS ADMINS
        // $user=$security->getUser();

            if ($spot){
                $entityManager->remove($spot);
                $entityManager->flush();

            }
            return $this->redirectToRoute('app_admin');
            
    }
    
    // Pour supprimer une photo d'un spot
    #[Route('/admin/deletePicture/{idSpot}/{idPic}', name: 'deletePicture_admin')]
    public function deletePicture(Security $security, Spot $idSpot, Picture $idPic , ManagerRegistry $doctrine) : Response
    {   
        $spot = $idSpot;
        $picture = $idPic;

        $user = $security->getUser();
        // Définition du rôle 
        $userRole = $user->getRoles();
        // Définition du manager de doctrine
        $entityManager = $doctrine->getManager();
        // dd($userRole);
        //si le user est admin
        if(in_array('ROLE_ADMIN', $userRole)){
            // Supprimer l'entrée de Picture correspondante
            $entityManager->remove($picture);
            $entityManager->flush();
            
        }

       return $this->redirectToRoute('show_spot', ["idSpot"=>$spot->getId()]);
    }

    #[Route('/admin/validate/{id}', name: 'validateSpot_admin')]
    public function validateSpot(Security $security, EntityManagerInterface $entityManager, Spot $spot)
    {
        //RESTE A GERER LES CONDITIONS ADMINS
        // $user=$security->getUser();

        //Selectionne le spot a modifier en pointant l'id
        $myspot = $entityManager->getRepository(Spot::class)->findOneBy(['id'=>$spot]);

            // Si le isValidated == FALSE -> le passe en TRUE
            if ($myspot->getIsValidated() == false) {
                // Modifier la propriété de l'entité
                $myspot->setIsValidated(true);
                //prepare
                $entityManager->persist($myspot);
                // Persist les modifications dans la base de données
                $entityManager->flush();
            }
            // autrement si isValidated == TRUE -> le passe en FALSE
            else{
                // Modifier la propriété de l'entité
                $myspot->setIsValidated(false);
                //prepare
                $entityManager->persist($myspot);
                // Persist les modifications dans la base de données
                $entityManager->flush();
            }
        return $this->redirectToRoute('app_admin');
    }
    #[Route('/admin/modify/{id}', name: 'modifySpot_admin')]
    public function modifySpot(Security $security, EntityManagerInterface $entityManager, Spot $spot)
    {
        //RESTE A GERER LES CONDITIONS ADMINS
        // $user=$security->getUser();

        //Selectionne le spot a modifier en pointant l'id
        $myspot = $entityManager->getRepository(Spot::class)->findOneBy(['id'=>$spot]);

            
        return $this->redirectToRoute('app_admin');
    }
    

    //*************************************CRUD UTILISATEUR******************************************* */
    #[Route('/admin/listUsers', name: 'listUsers_admin')]
    public function listUsers(Security $security, ManagerRegistry $doctrine, Request $request)
    {
        $users = $doctrine->getRepository(User::class)->findBy([], ['registrationDate'=>'DESC']);

        return $this->render('admin/listUsers.html.twig', [
            'controller_name' => 'AdminController',
            'users' => $users
        ]);
    }
    #[Route('/admin/banUser/{id}', name: 'banUser_admin')]
    public function banUser(Security $security, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, User $user = null, Request $request): Response
    {
        //RESTE A GERER LES CONDITIONS ADMINS
        // $user=$security->getUser();

            if ($user && $user->isIsBanned() == 0){
                $user->setIsBanned(1);
            }else{
                $user->setIsBanned(0);
            }
            $entityManager->flush();
                return $this->redirectToRoute('listUsers_admin');
    }
    #[Route('/admin/deleteUser/{id}', name: 'deleteUser_admin')]
    public function deleteUser(Security $security, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, User $user = null, Request $request): Response
    {
    //RESTE A GERER LES CONDITIONS ADMINS
    // $user=$security->getUser();
        if ($user){
            $entityManager->remove($user);
            $entityManager->flush();
        }
        return $this->redirectToRoute('listUsers_admin');
    }
//***********************************CRUD MODULE****************** */
#[Route('/admin/deleteModule/{id}', name: 'deleteModule_admin')]
public function deleteModule(Security $security, EntityManagerInterface $entityManager, ManagerRegistry $doctrine, Module $module = null, Request $request): Response
{
    //RESTE A GERER LES CONDITIONS ADMINS
    // $user=$security->getUser();

    
        if ($module){
            $entityManager->remove($module);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_module');
    }


    #[Route('/admin', name: 'app_admin')]
    public function index(ManagerRegistry $doctrine, Spot $spot = null, Request $request): Response
    {
        $spots = $doctrine->getRepository(Spot::class)->findBy([], ['creationDate'=>'DESC']);

        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
            'spots' => $spots
        ]);
    }
}

