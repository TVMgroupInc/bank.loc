<?php

namespace App\Repository;

use App\Entity\DepositCommissionLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DepositCommissionLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepositCommissionLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepositCommissionLog[]    findAll()
 * @method DepositCommissionLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositCommissionLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepositCommissionLog::class);
    }

    // /**
    //  * @return DepositCommissionLog[] Returns an array of DepositCommissionLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?DepositCommissionLog
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
