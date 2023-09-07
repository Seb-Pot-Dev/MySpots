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
// Pour KNP PAGINATOR 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
// Pour les filtres en AJAX
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class SpotController extends AbstractController
{

    #[Route("/spot/{id}/edit", name: "edit_spot")]
    #[Route(
        '/spot',
        name: 'app_spot',
    )]
    public function index(Security $security, ManagerRegistry $doctrine, Spot $spot = null, Request $request, PictureService $pictureService, PaginatorInterface $paginator, SpotRepository $spotRepository): Response
    {
        // Définition des variables de base****************************************************************************************************************
        //on accède aux méthodes du manager de doctrine
        $entityManager = $doctrine->getManager();

        //Définition du User
        $user = $security->getUser();
        //récupère tout les spots de la BDD pour les marqueurs sur la carte
        $spots = $doctrine->getRepository(Spot::class)->findBy([], ['name' => 'ASC']);

        // Requête pour la pagination des spots***********************************************************************************************************************
        $spotsQuery = $doctrine->getRepository(Spot::class)
            ->createQueryBuilder('s')
            ->orderBy('s.name', 'ASC')
            ->getQuery();

        $paginationSpots = $paginator->paginate(
            $spotsQuery, //requete a paginer
            $request->query->getInt('page', 1), // 1= numéro de page pas défaut
            5 // Nombre d'elements par page
        );

        // Gestion des FILTRES ****************************************************************************************************************************
        // instancie un nouvel object SearchData
        $searchData = new SearchData();
        // création du formulaire SpotSearchType pour les filtres
        $formSearch = $this->createForm(SpotSearchType::class, $searchData);
        // interception du formulaire SpotSearchType lorsqu'une requete est fournie 
        $formSearch->handleRequest($request);
        // instancie une variable $sportFiltered 
        $spotsFiltered = $spots; // égale a $spots tant qu'aucun filtre n'as été renseigner.
        $filtersEmpty = false;

        if ($formSearch->isSubmitted() && $formSearch->isValid()) {
            $searchFilter = $searchData->search;
            // dd($searchFilter);
            $moduleFilter = $searchData->moduleFilter;
            // dd($moduleFilter);
            $officialFilter = $searchData->official;
            $coveredFilter = $searchData->covered;
            $orderCreation = $searchData->orderCreation;
            $spotsFiltered = $spotRepository->findByCriteria($searchFilter, $moduleFilter, $officialFilter, $coveredFilter, $orderCreation);
            if (empty($spotsFiltered)) {
                $filtersEmpty = true;
            }
        }
        // Récupération des spots pour créé les MARKERS ************************************************************************************************
        /*
        Construit un tableau associatif contenant le nom du spot comme clé.
        Chaque clé est associée a un tableau contenant sa latitude, sa longitude, 
        sa description et son état de validation comme valeur
        */

        // instancie un tableau $tab
        $tab = [];
        // Si des filtres ont été selectionné, $spotsFiltered n'est pas empty
        // dans ce cas,j'utilise $spotsFiltered pour créé les marqueurs
        if (!empty($spotsFiltered)) {
            foreach ($spotsFiltered as $aspot) {
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
            // Sinon, pas de filtres. J'utilise $spots pour créé les marqueurs (avec TOUS les spots de ma BDD)
        } else {
            foreach ($spots as $aspot) {
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

    // Vérifier si le client est connecté avec un compte
    if($user){
        //lorsqu'une requete est soumise, récupère les données
        $form->handleRequest($request);
        //assigne les donnée du formulaire soumis à une variable 
        $newspot = $form->getData();

        // Pour vérifier si le spot existe déjà (modification) ou si il est nouveau (création)
        //défini isNew comme null
        $isNew = null;
        //si mon spot n'existe pas, créé un nouveau spot.
        if (!$spot) {
            $spot = new Spot();
            //initialisation d'une variable $isNew qui permet plus tard de vérifier si le spot viens d'être créé
            $isNew = true;
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // assigne les donnée du formulaire soumis à une variable 
            $newspot = $form->getData();

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

            //Si le spot vient d'être créé, alors ->setIsValidated()+CreationDate()+Author()
            if ($isNew) {
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
            'formSearch' => $formSearch->createView(),
            'spotsFiltered' => $spotsFiltered,
            'filtersEmpty' => $filtersEmpty
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

                    //assigne les donnée du formulaire soumis à une variable 
                    $newComment = $formComment->getData();

                    //défini quel spot est concerné par le commentaire
                    $newComment->setSpotConcerned($spot);


                    //Si c'est un nouveau commentaire, assigne la date et l'autheur
                    if ($isNew) {
                        // Pour définir la date/heure actuelle comme date du commentaire
                        $now = new \DateTime();
                        $newComment->setDate($now);

                        //Pour définir l'author du comm comme étant le user qui soumet le formulaire
                        $user = $security->getUser();
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
