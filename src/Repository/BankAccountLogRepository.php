<?php

namespace App\Repository;

use App\Entity\BankAccountLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BankAccountLog|null find($id, $lockMode = null, $lockVersion = null)
 * @method BankAccountLog|null findOneBy(array $criteria, array $orderBy = null)
 * @method BankAccountLog[]    findAll()
 * @method BankAccountLog[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BankAccountLogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BankAccountLog::class);
    }

    // /**
    //  * @return BankAccountLog[] Returns an array of BankAccountLog objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BankAccountLog
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
