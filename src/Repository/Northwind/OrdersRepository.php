<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\Orders;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Orders>
 * @method Orders|null find($id, $lockMode = null, $lockVersion = null)
 * @method Orders|null findOneBy(array $criteria, array $orderBy = null)
 * @method Orders[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrdersRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(Orders::class));
    }

    /**
     * @return Orders[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.orderId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
