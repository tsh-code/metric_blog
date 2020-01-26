<?php

namespace App\Repository;

use App\Entity\SecondExample;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method SecondExample|null find($id, $lockMode = null, $lockVersion = null)
 * @method SecondExample|null findOneBy(array $criteria, array $orderBy = null)
 * @method SecondExample[]    findAll()
 * @method SecondExample[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SecondExampleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SecondExample::class);
    }

    // /**
    //  * @return SecondExample[] Returns an array of SecondExample objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SecondExample
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
