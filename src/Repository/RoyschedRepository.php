<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Roysched;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Roysched>
 */
class RoyschedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Roysched::class);
    }

    /**
     * Find a Roysched by title ID
     */
    public function findOneByTitleId(string $titleId): ?Roysched
    {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.title', 't')
            ->where('t.titleId = :titleId')
            ->setParameter('titleId', $titleId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return Roysched[] Returns an array of Roysched objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Roysched
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
