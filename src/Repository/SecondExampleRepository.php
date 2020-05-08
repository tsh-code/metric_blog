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
}
