<?php

namespace App\Repository\TransactionManagement;

use App\Entity\TransactionManagement\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getByName($value, $cat)
    {
        $query = $this->createQueryBuilder('P')
                    ->leftJoin('P.categories', 'C')
                    ->orWhere('P.name LIKE :name') 
                    ->setParameter('name', "%{$value}%")
                    ->orWhere('P.name LIKE :name') 
                    ->setParameter('name', "%".htmlentities($value)."%");
                    //->orderBy('l.id', 'ASC')
                    //->setMaxResults(10)
                    if($cat){
                        $query->andWhere('C.title = :cat')
                          ->setParameter('cat',$cat);
                    }
                    //$query->andWhere('F.isValid = :valid')->setParameter('valid',1)->orderBy('F.id','ASC');

        return $query->getQuery()->getResult();
    }
    
     public function getCat($id)
      {
        $qb = $this->createQueryBuilder('P');
        
        $qb->leftJoin('P.categories', 'C')
            ->where($qb->expr()->eq('C.id', $id));
            
        return $qb->getQuery()->getResult();
      }

    /*
    public function findOneBySomeField($value): ?Livraison
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
