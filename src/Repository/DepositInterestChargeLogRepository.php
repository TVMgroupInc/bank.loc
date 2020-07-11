<?php

namespace App\Repository;

use App\Entity\DepositInterestChargeLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DepositInterestChargeLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepositInterestChargeLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepositInterestChargeLog[]    findAll()
 * @method DepositInterestChargeLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositInterestChargeLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepositInterestChargeLog::class);
    }

    // /**
    //  * @return DepositInterestChargeLog[] Returns an array of DepositInterestChargeLog objects
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
    public function findOneBySomeField($value): ?DepositInterestChargeLog
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
