<?php

namespace App\Repository;

use App\Entity\Spot;
use App\Entity\Module;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Spot>
 *
 * @method Spot|null find($id, $lockMode = null, $lockVersion = null)
 * @method Spot|null findOneBy(array $criteria, array $orderBy = null)
 * @method Spot[]    findAll()
 * @method Spot[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpotRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Spot::class);
    }
    // public function findSpotsPaginated(int $page, string $slug, int $limit = 6): array
    // {
    //     // Pour que la limite soit toujours positive
    //     $limit = abs($limit);
        
    //     $result = [];

    //     $query = $this->getEntityManager()->createQueryBuilder('s')
    //         ->select('c', 's')
    //         ->from('App/Entity/Spot', 's')
    //         ->join('s.covered', 'c')
    //         ->where("c.slug = '$slug'")
    //         ;

    //     return $result;

    // }
    public function save(Spot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Spot $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Spot[] Returns an array of Spot objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//La fonction findByCriteria  renvoie un tableau d'objets Spot qui correspondent aux critères spécifiés
public function findByCriteria(
    ?string $searchFilter, 
    array $moduleFilter, 
    bool $official, 
    bool $covered, 
    bool $onlyValidated, 
    ?string $order): array
{
    // Crée un nouvel objet QueryBuilder pour construire la requête.
    $qb = $this->createQueryBuilder('s')
        // left join de la collection de notations pour permettre la sous requete de tri sur la note moyenne d'un spot
        ->leftJoin('s.notations', 'n'); 
        
    // Si choix d'afficher uniquement les spots validés
    if($onlyValidated){
        // dd($onlyValidated);
        $qb->andWhere('s.isValidated = true');
    }

    // Si un filtre de recherche est fourni pour le nom du spot...
    if ($searchFilter) {
        // ...ajoute une condition à la requête pour filtrer les spots par leur nom.
        $qb->andWhere('s.name LIKE :search')
            ->setParameter('search', '%' . $searchFilter . '%');
    }

// Vérifie si on a des filtres de modules.
if (!empty($moduleFilter)) {
    // Pour chaque module...
    foreach ($moduleFilter as $index => $module) {
        // Prépare une sous requête pour voir si un spot a ce module.
        $subQb = $this->createQueryBuilder("sq$index");
        
        // Cherche les spots avec le module.
        $subQb->select("1")
            ->innerJoin("sq$index.modules", "m$index")
            ->where("sq$index = s")
            ->andWhere("m$index = :module$index")
            ->setParameter("module$index", $module);

        // Ajoute cette vérification à notre requête principale.
        $qb->andWhere($qb->expr()->exists($subQb->getDQL()))
            ->setParameter("module$index", $module);
    }
}
    // Si l'option de tri "skatepark" est selectionnée 
    if ($official) {
        $qb->andWhere('s.official = true');
    }
    // Si l'option de tri "couvert" est selectionnée
    if ($covered) {
        $qb->andWhere('s.covered = true');
    }
    // Si une des options trier par date d'ajout est selectionnée'
    if ($order == 'dateLast') {
        $qb->orderBy('s.creationDate', 'ASC');
    }elseif ($order == 'dateNew'){
        $qb->orderBy('s.creationDate', 'DESC');
    //tri par notation decroissante
    }elseif ($order == 'noteAsc'){
        $qb->addSelect('AVG(n.note) as HIDDEN avg_note')
        ->groupBy('s.id')
        ->orderBy('avg_note', 'ASC');
    // tri par notation croissante
    }elseif ($order == 'noteDesc'){
        $qb->addSelect('AVG(n.note) as HIDDEN avg_note')
            ->groupBy('s.id')
            ->orderBy('avg_note', 'DESC');
    }
    return $qb->getQuery()->getResult();
}

// La fonction findValidatedSpots renvoie les spots validés par l'administrateur uniquement.
public function findValidatedSpots(): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT s
            FROM App\Entity\Spot s
            WHERE s.isValidated = :validated'
        )->setParameter('validated', 1);

        return $query->getResult();
    }

//    public function findOneBySomeField($value): ?Spot
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
