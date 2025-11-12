<?php

namespace App\Repository\Northwind;

use App\Entity\Northwind\Employees;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends EntityRepository<Employees>
 * @method Employees|null find($id, $lockMode = null, $lockVersion = null)
 * @method Employees|null findOneBy(array $criteria, array $orderBy = null)
 * @method Employees[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmployeesRepository extends EntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        $entityManager = $registry->getManager('northwind');
        parent::__construct($entityManager, $entityManager->getClassMetadata(Employees::class));
    }
    /**
     * @return Employees[]
     */
    public function findAll(): array
    {
        return $this->createQueryBuilder('e')
            ->orderBy('e.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findByName(string $search): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.firstName LIKE :search')
            ->orWhere('e.lastName LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('e.lastName', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
