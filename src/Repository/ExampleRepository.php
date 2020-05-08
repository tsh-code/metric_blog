<?php

namespace App\Repository;

use App\Entity\Example;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Example|null find($id, $lockMode = null, $lockVersion = null)
 * @method Example|null findOneBy(array $criteria, array $orderBy = null)
 * @method Example[]    findAll()
 * @method Example[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExampleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Example::class);
    }
}
