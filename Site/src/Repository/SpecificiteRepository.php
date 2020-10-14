<?php

namespace App\Repository;

use App\Entity\Specificite;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Specificite|null find($id, $lockMode = null, $lockVersion = null)
 * @method Specificite|null findOneBy(array $criteria, array $orderBy = null)
 * @method Specificite[]    findAll()
 * @method Specificite[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SpecificiteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Specificite::class);
    }

    public function findAll()
    {
        return $this->findBy(array(), array('libellespe' => 'ASC'));
    }

    // /**
    //  * @return Specificite[] Returns an array of Specificite objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Specificite
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
