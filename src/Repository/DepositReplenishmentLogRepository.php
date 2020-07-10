<?php

namespace App\Repository;

use App\Entity\DepositReplenishmentLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DepositReplenishmentLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepositReplenishmentLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepositReplenishmentLog[]    findAll()
 * @method DepositReplenishmentLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositReplenishmentLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepositReplenishmentLogRepository::class);
    }

    // /**
    //  * @return ReplenishmentLog[] Returns an array of ReplenishmentLog objects
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
    public function findOneBySomeField($value): ?ReplenishmentLog
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
