<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\Categories;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Categories>
 * @method Categories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Categories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Categories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoriesRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(Categories::class));
    }

    /**
     * @return Categories[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.categoryName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
