<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Form\SpotType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SpotController extends AbstractController
{
    #[Route('/spot', name: 'app_spot')]
    public function index(ManagerRegistry $doctrine, Spot $spot = null, Request $request): Response
    {

        $spots = $doctrine->getRepository(Spot::class)->findBy([], ['name'=>'ASC']);

        //Créer un tableau $tab
        //Construit un tableau associatif contenant le nom du spot comme clé.
        //Chaque clé est associée a un tableau contenant sa latitude, sa longitude et sa description comme valeur
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

        //encode le tableau $tab en format JSON
        /*Le deuxième argument JSON_HEX_APOS de la fonction json_encode() permet de remplacer les single quotes
        par des entités HTML hexadécimales, pour éviter des problèmes de syntaxe dans le code JavaScript.*/
        $tabCoords = json_encode($tab, JSON_HEX_APOS);

        
            //Formulaire pour ajouter un spot

            /* *
            * LES PROBLEMES : LORS DE LAJOUT DUN SPOT, REMPLACE LE DERNIER AJOUT
            *
            */

                //Construire un formulaire qui se repose sur le $builder présent dans SpotType
                $form = $this->createForm(SpotType::class, $spot);
                //Qd il y a une action dans le for, analyse ce que récupère la requete
                $form->handleRequest($request);

                    //Si le formulaire est soumis et passe les filtres
                    if ($form->isSubmitted() && $form->isValid()) {
                        //récupère les données du formulaire et les injecte "hydrate" via les setter de l'objet 
                        $spot = $form->getData();
                        //on récupère le manager de doctrine pour accéder aux méthodes suivantes
                        $entityManager = $doctrine->getManager();
                        //On prépare notre requete
                        $entityManager->persist($spot);
                        //on execute notre recete pour insérer l'entrée en BDD
                        $entityManager->flush();
                    }

        //retourne la réponse http affichée par le navigateur.
        // 'spots' est le tableau des spots encodé en JSON.
        // 'spotsList' est le tableau contenant toutes les spots et toutes les infos des spots, pour la liste des spots
        return $this->render('spot/index.html.twig', [
            'controller_name' => 'SpotController',
            'spots' => $tabCoords,
            'spotsList' => $spots,
            'form' => $form->createView()

        ]);
    }
    #[Route('/spot/{id}', name: 'show_spot')]
    public function show(Spot $spot = null): Response
    //On appel l'objet Spot dont l'id est passé en parametre par la route
    {        
        if ($spot){

            return $this->render('spot/show.html.twig', [
                'controller_name' => 'SpotController',
                'spot' => $spot,
            ]);
        }
    }
}
