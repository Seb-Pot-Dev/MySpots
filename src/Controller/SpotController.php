<?php

namespace App\Controller;

use App\Entity\Spot;
use App\Entity\User;
use App\Entity\Module;
use App\Form\SpotType;
use App\Entity\Comment;
use App\Entity\Picture;
use App\Entity\Notation;
use App\Form\CommentType;
use App\Form\NotationType;
use App\Form\PictureType;
use App\Service\PictureService;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
// Pour KNP PAGINATOR 
use Knp\Component\Pager\PaginatorInterface;

class SpotController extends AbstractController
{

    #[Route("/spot/{id}/edit", name:"edit_spot")]
    #[Route('/spot', name: 'app_spot',
     )]  
    public function index(Security $security, ManagerRegistry $doctrine, Spot $spot = null, Request $request, PictureService $pictureService, PaginatorInterface $paginator): Response
    {
        //on accède aux méthodes du manager de doctrine
        $entityManager = $doctrine->getManager();


        //récupère tout les spots de la BDD pour les marqueurs sur la carte
        $spots = $doctrine->getRepository(Spot::class)->findBy([], ['name'=>'ASC']);


        // Requête pour la pagination des spots
        $spotsQuery = $doctrine->getRepository(Spot::class)
            ->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        $paginationSpots = $paginator->paginate(
            $spotsQuery, //requete a paginer
            $request->query->getInt('page', 0), // numéro de page pas défaut
            5 // Nombre d'elements par page
        );
        
        
        //Définition du User
        $user=$security->getUser();

        
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
                    $aspot->getId(),
                    $aspot->getAvgNote(),
                    $aspot->getPictures(),
                    $aspot->isCovered(),
                    $aspot->isOfficial()
                ]
            ];
        }
        

        /*encode le tableau $tab en format JSON
        Le deuxième argument JSON_HEX_APOS de la fonction json_encode() permet de remplacer les single quotes
        par des entités HTML hexadécimales, pour éviter des problèmes de syntaxe dans le code JavaScript.*/
        $tabCoords = json_encode($tab, JSON_HEX_APOS);

        
/**********************AJOUT DE SPOT*********************************** */        
        //récupérer la liste de tout les modules
        $modules = $doctrine->getRepository(Module::class)->findAll();

        //créé un formulaire qui se repose sur le builder (qui se repose lui mm sur les propriétés de la classe) et assigner la liste de tout les modules pour les checkboxs
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

        if($form->isSubmitted() && $form->isValid() && $user){
            // assigne les donnée du formulaire soumis à une variable 
            $newspot = $form->getData();
            
            // On récupère les images
            $images = $form->get('pictures')->getData();
            
            foreach($images as $image){
                // On défini le dossier de destination
                $folder = 'photos-spot';

                // On appel le service d'ajout
                $fichier = $pictureService->add($image, $folder, 300, 300);

                // On instancie un nouvel objet image
                $img = new Picture();
                // On lui assigne un nom (renvoyé par le service)
                $img->setName($fichier);
                $img->setSpot($newspot);

                // ajoute l'image au spot
                $newspot->addPicture($img);
            }

                //Si le spot vient d'être créé, alors ->setIsValidated()+CreationDate()+Author()
                if($isNew){
                    //Pour définir isValidated comme étant false
                    $newspot->setIsValidated(false);
        
                    // Pour définir la date/heure actuelle comme date d'inscription
                    $now = new \DateTime();
                    $newspot->setCreationDate($now);
        
                    //Pour définir l'author du spot comme étant le user qui soumet le formulaire
                    $newspot->setAuthor($user);

                }

            //prepare
            $entityManager->persist($newspot);
            //execute
            $entityManager->flush();
            //refresh la page
            return $this->redirectToRoute('app_spot');
        }
/********************** FIN AJOUT DE SPOT*********************************** */

        //retourne la réponse http affichée par le navigateur.
        // 'spots' est le tableau des spots encodé en JSON.
        // 'spotsList' est le tableau contenant toutes les spots et toutes les infos des spots, pour la liste des spots
        return $this->render('spot/index.html.twig', [
            'controller_name' => 'SpotController',
            'spots' => $tabCoords,
            'spotsList' => $spots,
            'paginationSpots' => $paginationSpots,
            'formAddSpot' => $form->createView(),
            'modules' => $modules,
        ]);
    }

    //Pour liker un post (ajouter une spot à user.favoriteSpot / un user a spot.favoritedByUser)
    #[Route('/spot/like/{idSpot}/{idUser}', name: 'like_spot')]
    #[ParamConverter("spot", options:["mapping"=>["idSpot"=>"id"]])]
    #[ParamConverter("user", options:["mapping"=>["idUser"=>"id"]])]
    public function likeSpot(Security $security, ManagerRegistry $doctrine, Spot $spot, User $user = null)
    {   
        $entityManager=$doctrine->getManager();

        if($user){
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
        }
        // ELSE voir pour mettre condition si user non connecté
        return $this->redirectToRoute('app_spot');  
    }

    #[Route('/spot/{idSpot}', name: 'show_spot')]
    #[ParamConverter("spot", options:["mapping"=>["idSpot"=>"id"]])]
    public function show(Security $security, Spot $spot = null, ManagerRegistry $doctrine, Comment $comment = null, Request $request, Request $requestNotation, Notation $notation= null, PictureService $pictureService): Response
    //On appel l'objet Spot dont l'id est passé en parametre par la route
    {       
        //on accède aux méthodes du manager de doctrine
        $entityManager = $doctrine->getManager();
        
        //si le spot existe
        if ($spot){
            //créé un formulaire pour ajouter/modifier les commentaires
            $formComment = $this->createForm(CommentType::class, $comment);
            //créé un formulaire pour créer une entité Notation
            $formNotation = $this->createForm(NotationType::class, $notation);
            //créé un formulaire qui se repose sur le builder
            $formPicture = $this->createForm(PictureType::class);

/* Pour le formulaire de Comment */
            //intercepte la requete du formulaire soumis
            $formComment->handleRequest($request);

            //assigne les donnée du formulaire soumis à une variable 
            $newComment = $formComment->getData();

                //défini isNew comme null (pour CREER)
                $isNew = false;
                //si mon comment n'existe pas, créé un nouveau commentaire.
                if(!$comment){
                    $comment = new Comment();
                    $isNew = true;
                }
            
            //Si le formulaire est soumis ET valide
            if($formComment->isSubmitted() && $formComment->isValid()){
            
                //assigne les donnée du formulaire soumis à une variable 
                $newComment = $formComment->getData();

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
                return $this->redirectToRoute('show_spot', ["idSpot"=>$spot->getId()]);
            }
/* Pour le formulaire de Notation */
// Vérifier si l'USER a déjà noté ce SPOT
            $formNotation->handleRequest($requestNotation);
            $newNotation = $formNotation->getData();
            $newNotation = new Notation();
            //Si le formulaire de NOTATION est soumis et valide
            if($formNotation->isSubmitted() && $formNotation->isValid()){

                $newNotation = $formNotation->getData();

                $user=$security->getUser();

                $newNotation->setSpot($spot);
                $newNotation->setUser($user);

                //prepare la requette
                $entityManager->persist($newNotation);
                //execute pour ajouter le comm en BDD
                $entityManager->flush();

                return $this->redirectToRoute('show_spot', ["idSpot"=>$spot->getId()]);
            }

            
            // On intercepte le formulaire d'image soumis
            $formPicture->handleRequest($request);
            //Si le formulaire de PICTURE est soumis et valide
            if($formPicture->isSubmitted() && $formPicture->isValid()){
                // On récupère les images
                $images = $formPicture->get('pictures')->getData();
                            
                    foreach($images as $image){
                        // On défini le dossier de destination
                        $folder = 'photos-spot';
                        // On appel le service d'ajout
                        $fichier = $pictureService->add($image, $folder, 300, 300);
                        // On instancie un nouvel objet image
                        $img = new Picture();
                        // On lui assigne un nom (renvoyé par le service) et un spot (défini via l'ID dans l'url ?)
                        $img->setName($fichier);
                        $img->setSpot($spot);

                        // On ajoute les images au spot
                        $spot->addPicture($img);
                    }
                //prepare la requette
                $entityManager->persist($img);
                //execute pour ajouter la picture en BDD
                $entityManager->flush();

                return $this->redirectToRoute('show_spot', ["idSpot"=>$spot->getId()]);
                }

            return $this->render('spot/show.html.twig', [
                'controller_name' => 'SpotController',
                'spot' => $spot,
                'formCommentSpot' => $formComment->createView(),
                'formNotation' => $formNotation->createView(),
                'formPicture' => $formPicture->createView(),
            ]);
        }
    }
    
}