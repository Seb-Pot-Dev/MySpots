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

//La fonction findByModules vous renvoie un tableau (array) d'objets Spot qui correspondent aux critères spécifiés par les paramètres $name et $moduleNames.
public function findByCriteria(?string $searchFilter, array $moduleFilter, bool $official, bool $covered, ?string $orderCreationDate): array
{
    // Crée un nouvel objet QueryBuilder pour construire la requête.
    $qb = $this->createQueryBuilder('s')
        // left join de la collection de notations pour permettre la sous requete de tri sur la note moyenne d'un spot
        ->leftJoin('s.notations', 'n');  

    // Ajout de la condition pour les spots validés
    $qb->andWhere('s.isValidated = 1');

    // Si un filtre de recherche est fourni pour le nom du spot...
    if ($searchFilter !== null) {
        // ...ajoute une condition à la requête pour filtrer les spots par leur nom.
        $qb->andWhere('s.name LIKE :search')
            ->setParameter('search', '%' . $searchFilter . '%');
    }

    // Si des modules sont fournis pour filtrage...
    if (!empty($moduleFilter)) {
        // ...traite chaque module individuellement.
        foreach ($moduleFilter as $index => $module) {
            // Crée une sous-requête pour vérifier l'association du spot avec le module actuel.
            $subQb = $this->createQueryBuilder("sq$index");
            
            // Sélectionne une valeur constante (1) pour la sous-requête.
            $subQb->select("1")
                // Fait une jointure avec la table des modules pour le spot.
                ->innerJoin("sq$index.modules", "m$index")
                // Assure que le spot dans la sous-requête est le même que le spot dans la requête principale.
                ->where("sq$index = s")
                // Ajoute une condition pour vérifier que le module actuel est associé au spot.
                ->andWhere("m$index = :module$index")
                ->setParameter("module$index", $module);

            // Ajoute une condition à la requête principale pour s'assurer que le spot est associé au module actuel.
            // Utilise la sous-requête pour cette condition.
            $qb->andWhere($qb->expr()->exists($subQb->getDQL()))
                ->setParameter("module$index", $module);
        }
    }
    // SI l'option de tri "skatepark" est selectionnée 
    if ($official) {
        $qb->andWhere('s.official = true');
    }
    // Si l'option de tri "couvert" est selectionnée
    if ($covered) {
        $qb->andWhere('s.covered = true');
    }
    // Si une des options trier par date d'ajout est selectionnée'
    if ($orderCreationDate == 'dateLast') {
        $qb->orderBy('s.creationDate', 'ASC');
    }elseif ($orderCreationDate == 'dateNew'){
        $qb->orderBy('s.creationDate', 'DESC');
    //tri par notation decroissante
    }elseif ($orderCreationDate == 'noteDesc'){
        $qb->addSelect('AVG(n.note) as HIDDEN avg_note')
        ->groupBy('s.id')
        ->orderBy('avg_note', 'DESC');
    // tri par notation croissante
    }elseif ($orderCreationDate == 'noteAsc'){
        $qb->addSelect('AVG(n.note) as HIDDEN avg_note')
            ->groupBy('s.id')
            ->orderBy('avg_note', 'ASC');
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
