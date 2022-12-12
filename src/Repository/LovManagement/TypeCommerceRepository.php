<?php

namespace App\Repository\LovManagement;

use App\Entity\LovManagement\TypeCommerce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TypeCommerceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeCommerce::class);
    }

    // public function getCountUse($gender)
    // {
    //     $entityManager = $this->getEntityManager();
    //     $query = $entityManager->createQuery('SELECT COUNT(U)
    //             FROM App\Entity\UserManagement\User U
    //             WHERE U.gender =:gender')
    //             ->setParameter('gender', $gender)
    //             ->setMaxResults(1)
    //             ->getSingleScalarResult();

    //     return $query;
    // }
}
