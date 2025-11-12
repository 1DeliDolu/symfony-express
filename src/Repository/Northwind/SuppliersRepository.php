<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\Suppliers;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Suppliers>
 * @method Suppliers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Suppliers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Suppliers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SuppliersRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(Suppliers::class));
    }

    /**
     * @return Suppliers[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.companyName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
