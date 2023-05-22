<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\User;
use App\Entity\Module;
use App\Form\SpotType;
use App\Entity\Comment;
use App\Form\CommentType;
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
    #[Route('/spot', name: 'app_spot',
    //  methods: ['GET']//TEST ici ajout de methods:get
     )]  
    public function index(Security $security, ManagerRegistry $doctrine, Spot $spot = null, Request $request): Response
    {
        //on accède aux méthodes du manager de doctrine
        $entityManager = $doctrine->getManager();

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
                    $aspot->getIsValidated(),
                    $aspot->getId()
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

        //assigne les donnée du formulaire soumis à une variable 
        $newspot = $form->getData();
        
        //défini isNew comme null
        $isNew=null;
        //si mon spot n'existe pas, créé un nouveau spot.
        if(!$spot){
            $spot = new Spot();
            //initialisation d'une variable $isNew qui permet plus tard de vérifier si le spot viens d'être créé
            $isNew = true;
        }

        if($form->isSubmitted() && $form->isValid()){
            $newspot = $form->getData();
            
            
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

//TEST

    //     $moduleIds = $request->query->get('modulesFilter', []);

    //     // Vérifier si des modules ont été sélectionnés
    // if (!is_array($moduleIds)) {
    //     $moduleIds = [$moduleIds];
    // }
    //     // Effectuer la recherche des spots par modules
    //     $spotsFiltered = $entityManager->getRepository(Spot::class)->findByModules($moduleIds);

    //     // Récupérer tous les modules disponibles pour l'affichage initial
    //     $modulesFilter = $entityManager->getRepository(Module::class)->findAll();
//FIN TEST 

        //retourne la réponse http affichée par le navigateur.
        // 'spots' est le tableau des spots encodé en JSON.
        // 'spotsList' est le tableau contenant toutes les spots et toutes les infos des spots, pour la liste des spots
        return $this->render('spot/index.html.twig', [
            'controller_name' => 'SpotController',
            'spots' => $tabCoords,
            'spotsList' => $spots,
            'formAddSpot' => $form->createView(),
            // 'edit' => $spot->getId(),
            'modules' => $modules,
            // 'modulesFilter' => $modulesFilter, //TEST
            // 'spotsFiltered' => $spotsFiltered //TEST
        ]);
    }

    // TEST **********************************************
    #[Route('/spots/filter', name: 'filter_spots', methods: ['GET'])]
    public function filter(Request $request): Response
    {
        $moduleIds = $request->query->get('modules', []);

        // Effectuer la recherche des spots par modules
        $spots = $this->getDoctrine()->getRepository(Spot::class)->findByModules($moduleIds);

        // Récupérer tous les modules disponibles pour l'affichage initial
        $modules = $this->getDoctrine()->getRepository(Module::class)->findAll();

        // Rendre la vue avec les résultats de la recherche et les modules disponibles
        return $this->render('spot/index.html.twig', [
            'spots' => $spots,
            'modules' => $modules,
        ]);
    }
    // TEST **********************************************

    

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

    #[Route('/spot/{id}', name: 'show_spot')]
    public function show(Security $security, Spot $spot = null, ManagerRegistry $doctrine, Comment $comment = null, Request $request): Response
    //On appel l'objet Spot dont l'id est passé en parametre par la route
    {       
        //on accède aux méthodes du manager de doctrine
        $entityManager = $doctrine->getManager();
        
        //si le spot existe
        if ($spot){

            //créé un formulaire pour ajouter/modifier les commentaires
            $form = $this->createForm(CommentType::class, $comment);

            //intercepte la requete du formulaire soumis
            $form->handleRequest($request);

            //assigne les donnée du formulaire soumis à une variable 
            $newComment = $form->getData();

                //défini isNew comme null (pour CREER)
                $isNew = null;
                //si mon comment n'existe pas, créé un nouveau commentaire.
                if(!$comment){
                    $comment = new Comment();
                    $isNew = true;
                }
            
            //Si le formulaire est soumis ET valide
            if($form->isSubmitted() && $form->isValid()){
            
                //défini quel spot est concerné par le commentaire
                $newComment->setSpotConcerned($spot);

                

                //Si c'est un nouveau commentaire, assigne la date et l'autheur
                if($isNew){
                    // Pour définir la date/heure actuelle comme date du commentaire
                    $now = new \DateTime();
                    $newComment->setDate($now);
    
                    //Pour définir l'author du comm comme étant le user qui soumet le formulaire
                    $user=$security->getUser();
                    $newComment->setAuthor($user);
                }

            //prepare la requette
            $entityManager->persist($newComment);
            //execute pour ajouter le comm en BDD
            $entityManager->flush();
            //refresh la page
            return $this->redirectToRoute('show_spot', ["id"=>$spot->getId()]);
            }

            return $this->render('spot/show.html.twig', [
                'controller_name' => 'SpotController',
                'spot' => $spot,
                'formCommentSpot' => $form->createView(),
                // 'comments' => $comments
            ]);
        }
    }
}
