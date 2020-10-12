<?php

namespace App\Repository;

use App\Entity\Intervient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Intervient|null find($id, $lockMode = null, $lockVersion = null)
 * @method Intervient|null findOneBy(array $criteria, array $orderBy = null)
 * @method Intervient[]    findAll()
 * @method Intervient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IntervientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Intervient::class);
    }

    // /**
    //  * @return Intervient[] Returns an array of Intervient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Role
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
