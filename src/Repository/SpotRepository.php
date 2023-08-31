<?php

namespace App\Repository;

use App\Entity\Spot;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
public function findByModules($name = null, $moduleTypes = []): array
{
    $qb = $this->createQueryBuilder('s');

    if ($name) {
        $qb->andWhere('s.name LIKE :name')
           ->setParameter('name', '%'.$name.'%');
    }

    if (!empty($moduleTypes)) {
        $qb->leftJoin('s.modules', 'm')
           ->andWhere('m.type IN (:types)')
           ->setParameter('types', $moduleTypes);
    }

    return $qb->getQuery()->getResult();
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
