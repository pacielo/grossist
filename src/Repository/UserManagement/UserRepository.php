<?php

namespace App\Repository\UserManagement;

use App\Entity\UserManagement\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserRepository extends ServiceEntityRepository
{
    private $translator;

    public function __construct(ManagerRegistry $registry, TranslatorInterface $translator)
    {
        parent::__construct($registry, User::class);
        $this->translator = $translator;
    }
   

    public function findAllByRole(string $role, $isEnable = null, $isValid = null)
    {
        $queryBuilder = $this->createQueryBuilder('u')
                            ->where('u.roles LIKE :role') //roles =json
                            ->andWhere('H.id != 134')
                            ->andWhere('H.id != 135')
                            ->setParameter('role', '%"' . $role . '"%')
            ;
            

        if($isEnable === 0 || $isEnable === 1){
            $queryBuilder->andWhere('u.isEnable = :isEnable')
                         ->setParameter('isEnable',  $isEnable);
        }
       
        if($isValid === 1 || $isValid === 0){
            $queryBuilder->andWhere('u.isValid = :isValid')
                         ->setParameter('isValid',  $isValid); 
        }

        $limit = 1000;
        $offset = 0;


        while (true) {
            $queryBuilder->setFirstResult($offset);
            $queryBuilder->setMaxResults($limit);

            $users = $queryBuilder->getQuery()->getResult();

            if (count($users) === 0) {
                break;
            }

            foreach ($users as $user) {
                yield $user;
                $this->_em->detach($user);
            }

            $offset += $limit;
        }
    }

    public function findAllByTwoRoles(string $role1, string $role2, $isEnable = null, $isValid = null)
    {
        $queryBuilder=  $this->createQueryBuilder('u')
             ->where('u.roles LIKE :role1 OR u.roles LIKE :role2') 
             ->setParameters(['role1' => '%'.$role1.'%',
                              'role2' => '%'.$role2.'%'])
        ;            

        if($isEnable === 0 || $isEnable === 1){
            $queryBuilder->andWhere('u.isEnable = :isEnable')
                         ->setParameter('isEnable',  $isEnable);
        }
       
        if($isValid === 1 || $isValid === 0){
            $queryBuilder->andWhere('u.isValid = :isValid')
                         ->setParameter('isValid',  $isValid); 
        }
        return $queryBuilder->getQuery()->getResult();
    }



    public function findAllByRoles(array $roles, $isEnable = null, $isValid = null)
    {
        $index = 1;
        $parameters = [];
        foreach ($roles as $role) {
            if ($index == 1) {
                $where = 'u.roles LIKE :role'.$index;
            } else {
                $where .= ' OR u.roles LIKE :role'.$index;
            }
            $parameters['role'.$index] = '%'.$role.'%';
            $index++;
        }          

        $queryBuilder=  $this->createQueryBuilder('u')
             ->where($where) 
             ->setParameters($parameters)
        ;            

        if($isEnable === 0 || $isEnable === 1){
            $queryBuilder->andWhere('u.isEnable = :isEnable')
                         ->setParameter('isEnable',  $isEnable);
        }
       
        if($isValid === 1 || $isValid === 0){
            $queryBuilder->andWhere('u.isValid = :isValid')
                         ->setParameter('isValid',  $isValid); 
        }
        return $queryBuilder->getQuery()->getResult();


    }



    public function findAllButRoles(array $roles) 
    {
        $query= $this->createQueryBuilder('u')
                    ->where('u.roles NOT LIKE :role1 OR u.roles NOT LIKE :role2') //roles =json
                    ->andWhere('u.isEnable = true') 
                    ->andWhere('u.isValid = true') 
                    ->setParameters([
                            'role1' => 'ROLE_GERANT',
                            'role2' => 'ROLE_TRANS'
                        ]
                    )
            ;

        return $query->getQuery()->getResult();
    }

    public function findAllValidByRole(string $role)
    {
        $queryBuilder = $this->createQueryBuilder('u')
             ->where('u.roles LIKE :role') //roles =json
             ->andWhere('u.isEnable = true')
             ->andWhere('u.isValid = true')
             ->setParameter('role', '%"' . $role . '"%')
             ;

        $limit = 1000;
        $offset = 0;

        while (true) {
            $queryBuilder->setFirstResult($offset);
            $queryBuilder->setMaxResults($limit);

            $users = $queryBuilder->getQuery()->getResult();

            if (count($users) === 0) {
                break;
            }

            foreach ($users as $user) {
                yield $user;
                $this->_em->detach($user);
            }

            $offset += $limit;
        }
    }

}
