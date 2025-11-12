<?php

declare(strict_types=1);

namespace App\Repository\Pubs;

use App\Entity\Pubs\TitleAuthor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TitleAuthor>
 */
class TitleAuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TitleAuthor::class);
    }

    /**
     * Find a TitleAuthor by author ID and title ID
     */
    public function findOneByAuthorAndTitle(string $authorId, string $titleId): ?TitleAuthor
    {
        return $this->createQueryBuilder('ta')
            ->innerJoin('ta.author', 'a')
            ->innerJoin('ta.title', 't')
            ->where('a.auId = :authorId')
            ->andWhere('t.titleId = :titleId')
            ->setParameter('authorId', $authorId)
            ->setParameter('titleId', $titleId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    //    /**
    //     * @return TitleAuthor[] Returns an array of TitleAuthor objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('t.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?TitleAuthor
    //    {
    //        return $this->createQueryBuilder('t')
    //            ->andWhere('t.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
