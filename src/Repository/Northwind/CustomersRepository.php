<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\Customers;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Customers>
 * @method Customers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(Customers::class));
    }

    /**
     * @return Customers[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('c')
            ->orderBy('c.companyName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
