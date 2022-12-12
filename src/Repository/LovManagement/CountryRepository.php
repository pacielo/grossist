<?php

namespace App\Repository\LovManagement;

use App\Entity\LovManagement\Country;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CountryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Country::class);
    }

    public function getCountUse($country)
    {
        $entityManager = $this->getEntityManager();
        $query = $entityManager->createQuery('SELECT COUNT(U)
                FROM App\Entity\UserManagement\User U
                WHERE U.country =:country')
                ->setParameter('country', $country)
                ->setMaxResults(1)
                ->getSingleScalarResult();

        return $query;
    }
}
