<?php

namespace App\Repository;

use App\Entity\DepositHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method DepositHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method DepositHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method DepositHistory[]    findAll()
 * @method DepositHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DepositHistory::class);
    }

    // /**
    //  * @return DepositHistory[] Returns an array of DepositHistory objects
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
    public function findOneBySomeField($value): ?DepositHistory
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
