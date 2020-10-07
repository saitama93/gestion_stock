<?php
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findLogin($login){
        return $this->getEntityManager()->createQuery('select u from App\Entity\User u WHERE u.username = :username')
            ->setParameter('username',$login)->getResult();
    }
}
