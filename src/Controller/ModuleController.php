<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\Module;
use App\Form\ModuleType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ModuleController extends AbstractController
{
    #[Route('/module', name: 'app_module')]
    #[Route('/module/{id}/edit', name: 'app_module_edit')]
    public function index(Security $security, ManagerRegistry $doctrine, Module $module = null, Request $request, $id=null): Response
    {
        // définition du $user actif
        $user=$this->getUser();
        // Si le $user possède le role admin
        if($user && $user->getRoles()==='["ROLE_ADMIN"]'){
            // Vérifie si un ID de module est fourni
            if ($id !== null) {
                // Récupére l'entité Module correspondante depuis la base de données
                $module = $doctrine->getRepository(Module::class)->find($id);
                
                // Vérifie si le module existe
                if (!$module) {
                    throw $this->createNotFoundException('Module non trouvé');
                }
            }
            //on accède aux méthodes du manager de doctrine
            $entityManager = $doctrine->getManager();

            //récupère tout les modules de la BDD. (liste de modules)
            $modules = $doctrine->getRepository(Module::class)->findBy([], ['name'=>'ASC']);

            //créé un formulaire qui se repose sur le builder (qui se repose lui mm sur les propriétés de la classe) et assigner la liste de tout les modules
            $form = $this->createForm(ModuleType::class, $module);

            //lorsqu'une requete est soumise, récupère les données
            $form->handleRequest($request);

            //assigne les donnée du formulaire soumis à une variable 
            $newModule = $form->getData();

            //si le formulaire est soumit
            if($form->isSubmitted() && $form->isValid()){
                //prepare
                $entityManager->persist($newModule);
                //execute
                $entityManager->flush();
                //refresh la page
                return $this->redirectToRoute('app_module');
            }
            return $this->render('admin/newModule.html.twig', [
                'modules' => $modules, //liste de tout les modules
                'formNewModule' => $form->createView()
            ]);
        }else{
            throw new \Exception('Accès reservé aux administrateurs connectés.');
            return $this->redirectToRoute('app_home');
        }
    }
}
