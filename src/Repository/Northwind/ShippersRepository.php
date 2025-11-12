<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\Shippers;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Shippers>
 * @method Shippers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shippers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shippers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShippersRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(Shippers::class));
    }

    /**
     * @return Shippers[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.companyName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
