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
use App\Form\PictureType;   
use App\Model\SearchData;
use App\Form\NotationType;
use App\Form\SpotSearchType;
use App\Service\PictureService;
use App\Repository\SpotRepository;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;

// Pour les filtres en AJAX
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpotController extends AbstractController
{

    #[Route("/spot/{id}/edit", name: "edit_spot")]
    #[Route("/spot", name: "app_spot")]
    public function index(Security $security, ManagerRegistry $doctrine, SpotRepository $spotRepository, Spot $spot = null, Request $request, PictureService $pictureService, PaginatorInterface $paginator): Response
    {
        // Définition des variables de base****************************************************************************************************************
        //on accède aux méthodes du manager de doctrine
        $entityManager = $doctrine->getManager();
        //Définition du User
        $user = $security->getUser();
        //récupère tout les spots de la BDD pour les marqueurs sur la carte
        $allSpots = $doctrine->getRepository(Spot::class)->findBy([], ['name' => 'ASC']);

        // // Requête pour la pagination des spots***********************************************************************************************************************
        // $spotsQuery = $doctrine->getRepository(Spot::class)
        //     ->createQueryBuilder('s')
        //     ->orderBy('s.name', 'ASC')
        //     ->getQuery();

        // $paginatedSpots = $paginator->paginate(
        //     $spotsQuery, //requete a paginer
        //     $request->query->getInt('page', 1), // 1= numéro de page pas défaut
        //     5 // Nombre d'elements par page
        // );

        // Gestion des FILTRES ******************************************************************************************************
        // initialisation d'un variable string vide
        $filtersEmptyMessage = '';
        // instancie un nouvel object SearchData
        $searchData = new SearchData();
        // création du formulaire SpotSearchType pour les filtres
        $formSearch = $this->createForm(SpotSearchType::class, $searchData);
        // interception du formulaire SpotSearchType lorsqu'une requete est fournie 
        $formSearch->handleRequest($request);

        // instancie une variable $sportFiltered 
        $spotsFiltered = $allSpots; // égale a $allSpots tant qu'aucun filtre n'as été renseigner.

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $searchFilter = $searchData->search;
            $moduleFilter = $searchData->moduleFilter;
            $officialFilter = $searchData->official;
            $coveredFilter = $searchData->covered;
            $order = $searchData->order;
            $onlyValidated = $searchData->onlyValidated;
            $spotsFiltered = $spotRepository->findByCriteria($searchFilter, $moduleFilter, $officialFilter, $coveredFilter, $onlyValidated, $order);
            // Si $spotFiltered est vide, c'est qu'aucun spot ne correspond aux critères selectionnées.
            if (empty($spotsFiltered)) {
                // On affecte un message pour en informer l'utilisateur
                $filtersEmptyMessage = "Aucun spots ne corresponds à ces critères.";
            }
        }
        /* Gestion des MARKER ********************************************/
        // Fonction pour récupérer la première photo ou retourner une chaîne vide
        function getFirstPictureName($spotMarker) {
            return count($spotMarker->getPictures()) > 0 ? $spotMarker->getPictures()[0]->getName() : '';
        }

        // Fonction pour créer un tableau associatif pour un spot donné
        function createSpotArray($spotMarker) {
            return [
                $spotMarker->getName() => [
                    $spotMarker->getLat(),
                    $spotMarker->getLng(),
                    $spotMarker->getDescription(),
                    $spotMarker->getIsValidated(),
                    $spotMarker->getId(),
                    $spotMarker->getAvgNote(),
                    getFirstPictureName($spotMarker),
                ]
            ];
        }

        // Initialisation d'une variable arraySpots en tant que tableau vide
        $arraySpots = [];

        // Détermine quel ensemble de spots utiliser
        $spotsToUse = !empty($spotsFiltered) && $formSearch->isSubmitted() && $formSearch->isValid() ? $spotsFiltered : $allSpots;

        // Boucle sur l'ensemble de spots choisi pour créer le tableau
        foreach ($spotsToUse as $spotMarker) {
            $arraySpots[] = createSpotArray($spotMarker);
        }

        // Encode le tableau $arraySpots en format JSON
        $arraySpotJson = json_encode($arraySpots);


/*AJOUT DE SPOT*********************************** */

    // Récupérer la liste de tous les modules
    $modules = $doctrine->getRepository(Module::class)->findAll();

    // Créer un formulaire basé sur SpotType et passer les modules
    $form = $this->createForm(SpotType::class, $spot, [
        'modules' => $modules
    ]);

    // Initialiser la variable pour vérifier si le spot est nouveau
    $isNew = !$spot;

    // Vérifier si le client est connecté avec un compte
    if($user){
        // Gérer la requête du formulaire
        $form->handleRequest($request);

        // Vérifier si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Assigner les données du formulaire à une variable
            $newspot = $form->getData();
            
            // Traiter les images
            // On récupère les images
            $images = $form->get('pictures')->getData();

            foreach ($images as $image) {
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


            // Configurer les propriétés du nouveau spot si nécessaire
            if ($isNew) {
                $newspot->setIsValidated(false)
                        ->setCreationDate(new \DateTime())
                        ->setAuthor($user);
            }
            
            // Préparer et exécuter la persistance des données
            $entityManager->persist($newspot);
            $entityManager->flush();
            
            // Rediriger vers la liste des spots
            return $this->redirectToRoute('app_spot');
        }
    }

        /********************** FIN AJOUT DE SPOT*********************************** */

        // Utilise la méthode render héritée de AbstractController pour retourner une réponse HTTP.
        // Cette réponse contient le contenu de la vue index.html.twig + des variables
        // 'spots' est le tableau des spots encodé en JSON.
        // 'spotsList' est le tableau contenant toutes les spots et toutes les infos des spots, pour la liste des spots
        // 'spotsFiltered' est le tableau des sports filtrés et ordonnés si des critères sont renseignés et que des occurences correspondent à ces derniers
        return $this->render('spot/index.html.twig', [
            'spots' => $arraySpotJson,
            'spotsList' => $allSpots,
            'formAddSpot' => $form->createView(),
            'modules' => $modules,
            'formSearch' => $formSearch->createView(),
            'spotsFiltered' => $spotsFiltered,
            'filtersEmptyMessage' => $filtersEmptyMessage
        ]);
    }

    //Pour liker un post (ajouter une spot à user.favoriteSpot / un user a spot.favoritedByUser)
    #[Route('/spot/like/{idSpot}/{idUser}', name: 'like_spot')]
    #[ParamConverter("spot", options: ["mapping" => ["idSpot" => "id"]])]
    #[ParamConverter("user", options: ["mapping" => ["idUser" => "id"]])]
    public function likeSpot(Security $security, ManagerRegistry $doctrine, Spot $spot, User $user = null)
    {
        $entityManager = $doctrine->getManager();

        if ($user) {
            //si l'utilisateur a déjà liké le spot
            if ($user->getFavoriteSpots()->contains($spot)) {

                //on supprime le user de la collection FavoritedByUser du spot
                $spot->removeFavoritedByUser($user);

                $entityManager->persist($spot);
                $entityManager->flush();
            }
            //sinon si l'utilisateur n'a pas encore liké
            else {

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
    #[ParamConverter("spot", options: ["mapping" => ["idSpot" => "id"]])]
    public function show(Security $security, Spot $spot = null, ManagerRegistry $doctrine, Comment $comment = null, Request $request, Request $requestNotation, Notation $notation = null, PictureService $pictureService): Response
    //On appel l'objet Spot dont l'id est passé en parametre par la route
    {
        //on accède aux méthodes du manager de doctrine
        $entityManager = $doctrine->getManager();
        // définition du user
        $user = $security->getUser();

        //si le spot existe
        if ($spot) {
            //créé un formulaire pour ajouter/modifier les commentaires
            $formComment = $this->createForm(CommentType::class, $comment);
            //créé un formulaire pour créer une entité Notation
            $formNotation = $this->createForm(NotationType::class, $notation);
            //créé un formulaire qui se repose sur le builder
            $formPicture = $this->createForm(PictureType::class);

            // et que le client est connecté
            if($user){
                /* Pour le formulaire de Comment */
                //intercepte la requete du formulaire soumis
                $formComment->handleRequest($request);

                //assigne les donnée du formulaire soumis à une variable 
                $newComment = $formComment->getData();

                //défini isNew comme null (pour CREER)
                $isNew = false;
                //si mon comment n'existe pas, créé un nouveau commentaire.
                if (!$comment) {
                    $comment = new Comment();
                    $isNew = true;
                }

                //Si le formulaire est soumis ET valide
                if ($formComment->isSubmitted() && $formComment->isValid()) {

                    //défini quel spot est concerné par le commentaire
                    $newComment->setSpotConcerned($spot);


                    //Si c'est un nouveau commentaire, assigne la date et l'autheur
                    if ($isNew) {
                        // Pour définir la date/heure actuelle comme date du commentaire
                        $now = new \DateTime();
                        $newComment->setDate($now);

                        //Pour définir l'author du comm comme étant le user qui soumet le formulaire
                        $newComment->setAuthor($user);
                    }

                    //prepare la requette
                    $entityManager->persist($newComment);
                    //execute pour ajouter le comm en BDD
                    $entityManager->flush();
                    //refresh la page
                    return $this->redirectToRoute('show_spot', ["idSpot" => $spot->getId()]);
                }
                /* Pour le formulaire de Notation */
                $formNotation->handleRequest($requestNotation);
                // Vérifier si le User a déjà noté ce Spot
                $existingNotation = $doctrine->getRepository(Notation::class)->findOneBy([
                    'spot' => $spot,
                    'user' => $user
                ]);
                // Si une notation existante est trouvée pour l'utilisateur et le spot, utilisez-la, sinon créez une nouvelle instance de Notation
                $notationToSave = $existingNotation ? $existingNotation : new Notation();

                //Si le formulaire de NOTATION est soumis et valide
                if ($formNotation->isSubmitted() && $formNotation->isValid()) {

                    if ($existingNotation) {
                        // L'utilisateur a déjà noté ce spot: mise à jour de la notation
                        $this->addFlash('info', 'Votre notation a été mise à jour.');
                    } else {
                        // Ajout d'une nouvelle notation
                        $notationToSave->setSpot($spot);
                        $notationToSave->setUser($user);
                    }

                    $notationToSave->setNote($formNotation->get('note')->getData()); // Ajoutez ceci si 'note' est un champ de votre formulaire. Ajustez en fonction des champs réels.

                    // Prépare la requête
                    $entityManager->persist($notationToSave);

                    // Exécute pour ajouter/actualiser la notation en BDD
                    $entityManager->flush();

                    return $this->redirectToRoute('show_spot', ["idSpot" => $spot->getId()]);
                }

                /*********** GESTION DES IMAGES ******/
                // On intercepte le formulaire d'image soumis
                $formPicture->handleRequest($request);
                //Si le formulaire de PICTURE est soumis et valide
                if ($formPicture->isSubmitted() && $formPicture->isValid()) {
                    // On récupère les images
                    $images = $formPicture->get('pictures')->getData();
                    foreach ($images as $image) {
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

                    return $this->redirectToRoute('show_spot', ["idSpot" => $spot->getId()]);
                }
            }
            return $this->render('spot/show.html.twig', [
                'controller_name' => 'SpotController',
                'spot' => $spot,
                'formCommentSpot' => $formComment->createView(),
                'formNotation' => $formNotation->createView(),
                'formPicture' => $formPicture->createView(),

            ]);
        }
        // Si le spot n'existe pas, redirection vers la map
        else {
            return $this->redirectToRoute('app_spot');
        }
    }
}
