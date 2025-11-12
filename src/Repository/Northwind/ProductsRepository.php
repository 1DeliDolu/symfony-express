<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\Products;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Products>
 * @method Products|null find($id, $lockMode = null, $lockVersion = null)
 * @method Products|null findOneBy(array $criteria, array $orderBy = null)
 * @method Products[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductsRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(Products::class));
    }

    /**
     * @return Products[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.productName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
