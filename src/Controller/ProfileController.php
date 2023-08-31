<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ProfileType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ProfileController extends AbstractController
{
    #[Route('/profile/my_profile', name: 'my_profile')]
    public function index(Security $security, ManagerRegistry $doctrine, User $user = null, Request $request): Response
    {
        $user = $this->getUser();

        if(!$user){
            throw new \Exception('Accès reservé aux utilisateurs connectés.');
            redirectToRoute('/home/index.html.twig');
        }
        return $this->render('profile/index.html.twig', [
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
    #[Route('/profile/editPseudo', name: 'edit_pseudo')]
    public function editPseudo(Security $security, ManagerRegistry $doctrine, User $user = null, Request $request, UserPasswordHasherInterface $hasher): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $security->getUser();

        // Si aucun user est connecté renvoie vers la page de connexion
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        // Si le user connecté n'est pas strictement le même que celui récupérer par la class Security
        if($this->getUser() !== $user) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(ProfileType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // password_verify($form->getData()->getPlainPassword(), $user->getPassword)
            // if($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())){
            // if(password_verify($form->get('plainPassword')->getData(), $user->getPassword())
            // ){
                $user = $form->getData();
                $entityManager->persist($user);
                $entityManager->flush();
                
                $this->addFlash(
                    'success',
                    'Les informations de votre compte ont bien été modifiées'
                );
                return $this->redirectToRoute('edit_pseudo');
            // }else{
            //     $this->addFlash(
            //         'warning',
            //         'Le mot de passe renseignée est incorrect'
            //     );
            // }
        }
        
        return $this->render('profile/edit_pseudo.html.twig', [
            'controller_name' => 'ProfileController',
            'user' => $user,
            'formEditProfile' => $form->createView()
        ]);
    }
    #[Route('/profile/removeAccount', name: 'remove_account')]
    public function removeAccount(Security $security, ManagerRegistry $doctrine, User $user = null, UserPasswordHasherInterface $hasher): Response
    {
        $entityManager = $doctrine->getManager();
        $user = $security->getUser();

        // Si aucun user est connecté renvoie vers la page de connexion
        if(!$this->getUser()){
            return $this->redirectToRoute('app_login');
        }

        // Si le user connecté n'est pas strictement le même que celui récupéré par la class Security
        if($this->getUser() !== $user) {
            return $this->redirectToRoute('app_home');
        }

        // Si le user connecté est le même que celui récupéré par la class Security
        if($this->getUser() == $user){
            // supprime l'objet User
            $entityManager->remove($user);
            $entityManager->flush();
            // supprime les informations de session pour éviter les conflits
            session_destroy();
        }
        
        return $this->redirectToRoute('app_home');
    }

}

