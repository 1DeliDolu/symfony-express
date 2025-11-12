<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\OrderDetails;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<OrderDetails>
 * @method OrderDetails|null find($id, $lockMode = null, $lockVersion = null)
 * @method OrderDetails|null findOneBy(array $criteria, array $orderBy = null)
 * @method OrderDetails[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderDetailsRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(OrderDetails::class));
    }

    /**
     * @return OrderDetails[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('od')
            ->orderBy('od.orderId', 'ASC')
            ->addOrderBy('od.productId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
