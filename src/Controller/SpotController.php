<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\User;
use App\Entity\Module;
use App\Form\SpotType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SpotController extends AbstractController
{

    #[Route("/spot/{id}/edit", name:"edit_spot")]
    #[Route('/spot', name: 'app_spot')]
    public function index(Security $security, ManagerRegistry $doctrine, Spot $spot = null, Request $request): Response
    {
        //récupère tout les spots de la BDD.
        $spots = $doctrine->getRepository(Spot::class)->findBy([], ['name'=>'ASC']);
        
        /*Créer un tableau $tab
        /Construit un tableau associatif contenant le nom du spot comme clé.
        /Chaque clé est associée a un tableau contenant sa latitude, sa longitude, 
        /sa description et son état de validation comme valeur*/
        $tab = [];
        foreach($spots as $aspot){
            $tab[] = [
                $aspot->getName() => [
                    $aspot->getLat(), 
                    $aspot->getLng(),
                    $aspot->getDescription(),
                    $aspot->getIsValidated()
                ]
            ];
        }

        /*encode le tableau $tab en format JSON
        Le deuxième argument JSON_HEX_APOS de la fonction json_encode() permet de remplacer les single quotes
        par des entités HTML hexadécimales, pour éviter des problèmes de syntaxe dans le code JavaScript.*/
        $tabCoords = json_encode($tab, JSON_HEX_APOS);

        
//-------------Formulaire pour ajouter/modifier un spot--------------------------
        
        //récupérer la liste de tout les modules
        $modules = $doctrine->getRepository(Module::class)->findAll();    

        //créé un formulaire qui se repose sur le builder (qui se repose lui mm sur les propriétés de la classe) et assigner la liste de tout les modules
        $form = $this->createForm(SpotType::class, $spot, [
            'modules' => $modules
        ]);
        //lorsqu'une requete est soumise, récupère les données
        $form->handleRequest($request);

        $newspot = $form->getData();
        
        $isNew=null;
        //si mon spot n'existe pas, créé un nouveau spot.
        if(!$spot){
            $spot = new Spot();
            //initialisation d'une variable $isNew qui permet plus tard de vérifier si le spot viens d'être créé
            $isNew = true;
        }

        if($form->isSubmitted() && $form->isValid()){
            $newspot = $form->getData();
            //on accède aux méthodes du manager de doctrine
            $entityManager = $doctrine->getManager();
            
                //Si le spot vient d'être créé, alors ->setIsValidated()+CreationDate()+Author()
                if($isNew){
                    //Pour définir isValidated comme étant false
                    $newspot->setIsValidated(false);
        
                    // Pour définir la date/heure actuelle comme date d'inscription
                    $now = new \DateTime();
                    $newspot->setCreationDate($now);
        
                    //Pour définir l'author du spot comme étant le user qui soumet le formulaire
                    $user=$security->getUser();
                    $newspot->setAuthor($user);
                }

            //prepare
            $entityManager->persist($newspot);
            //execute
            $entityManager->flush();
            //refresh la page
            return $this->redirectToRoute('app_spot');
        }


        //retourne la réponse http affichée par le navigateur.
        // 'spots' est le tableau des spots encodé en JSON.
        // 'spotsList' est le tableau contenant toutes les spots et toutes les infos des spots, pour la liste des spots
        return $this->render('spot/index.html.twig', [
            'controller_name' => 'SpotController',
            'spots' => $tabCoords,
            'spotsList' => $spots,
            'formAddSpot' => $form->createView(),
            'edit' => $spot->getId()
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
    //Pour liker un post (ajouter une spot à user.favoriteSpot / un user a spot.favoritedByUser)
    #[Route('/spot/like/{idSpot}/{idUser}', name: 'like_spot')]
    #[ParamConverter("spot", options:["mapping"=>["idSpot"=>"id"]])]
    #[ParamConverter("user", options:["mapping"=>["idUser"=>"id"]])]
    public function likeSpot(Security $security, ManagerRegistry $doctrine, Spot $spot, User $user = null)
    {   
        $entityManager=$doctrine->getManager();

        //si l'utilisateur a déjà liké le spot
        if($user->getFavoriteSpots()->contains($spot)){

            //on supprime le user de la collection FavoritedByUser du spot
            $spot->removeFavoritedByUser($user);
    
            $entityManager->persist($spot);
            $entityManager->flush();
        }
        //sinon si l'utilisateur n'a pas encore liké
        else{

            //on ajoute le user a la collection FavoritedByUser du spot
            $spot->addFavoritedByUser($user);
    
            $entityManager->persist($spot);
            $entityManager->flush();
        }


    
        return $this->redirectToRoute('app_spot');
        
    
    }
}
