<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\CustomerDemographics;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<CustomerDemographics>
 * @method CustomerDemographics|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerDemographics|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerDemographics[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerDemographicsRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(CustomerDemographics::class));
    }

    /**
     * @return CustomerDemographics[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('cd')
            ->orderBy('cd.customerTypeId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
