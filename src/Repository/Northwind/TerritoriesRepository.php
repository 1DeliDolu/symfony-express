<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\Territories;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Territories>
 * @method Territories|null find($id, $lockMode = null, $lockVersion = null)
 * @method Territories|null findOneBy(array $criteria, array $orderBy = null)
 * @method Territories[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TerritoriesRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(Territories::class));
    }

    /**
     * @return Territories[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.territoryId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
