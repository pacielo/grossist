<?php

namespace App\Repository\LovManagement;

use App\Entity\LovManagement\Civility;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CivilityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Civility::class);
    }

    public function getCountUse($civility)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT COUNT(U)
                FROM App\Entity\UserManagement\User U
                WHERE U.civility =:civility')
                ->setParameter('civility', $civility)
                ->setMaxResults(1)
                ->getSingleScalarResult();

        return $query;
    }
}
