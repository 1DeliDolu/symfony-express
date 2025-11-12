<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\CustomerCustomerDemo;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<CustomerCustomerDemo>
 * @method CustomerCustomerDemo|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerCustomerDemo|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerCustomerDemo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerCustomerDemoRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(CustomerCustomerDemo::class));
    }

    /**
     * @return CustomerCustomerDemo[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('ccd')
            ->orderBy('ccd.customerId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
