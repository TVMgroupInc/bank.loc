<?php

namespace App\Repository;

use App\Entity\BankAccountLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\DependencyInjection\Loader\Configurator\expr;

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

    /**
     * Monthly loss or profit of the bank. (Amount of commissions - Amount of accrued interest)
     * @param int|null $month
     * @param int|null $year
     * @return array|null
     * @throws DBALException
     */
    public function getBankBalanceByMonth(int $month = null, int $year = null): ?array
    {
        /* QUERY NOT WORKING with DATE_FORMAT in select and YEAR, MONTH in GROUP BY
        $qbBankBalance = $this->createQueryBuilder('bal');
        $qbBankBalance->select(
            'DATE_FORMAT(bal.date_ops, "%Y-%m") AS year_month_period',
            'SUM(bal.balance_change) * -1 bank_balance'
        );
        $qbBankBalance->where($qbBankBalance->expr()->in('bal.type_ops', ':type_ops'));

        if (!empty($month)) {
            $qbBankBalance->andWhere($qbBankBalance->expr()->eq('MONTH(bal.date_ops)', ':month'));
            $qbBankBalance->setParameter('month', $month);
        }

        if (!empty($year)) {
            $qbBankBalance->andWhere($qbBankBalance->expr()->eq('YEAR(bal.date_ops)', ':year'));
            $qbBankBalance->setParameter('year', $year);
        }
        $qbBankBalance->setParameter('type_ops', ['deposit_interest_charge', 'deposit_commision']);
        $qbBankBalance->groupBy('YEAR(bal.date_ops), MONTH(bal.date_ops)');
        $qbBankBalance->orderBy('bal.date_ops', 'ASC');

        try {
            $balanceRes = $qbBankBalance->getQuery()->getResult();
        } catch (\Exception $e) {
            throw $e;
        }*/
        $conn = $this->getEntityManager()->getConnection();
        $execute = [];

        $sql = '
        SELECT DATE_FORMAT(bal.date_ops, "%Y-%m") AS year_month_period, SUM(bal.balance_change) * -1 bank_balance
        FROM bank_account_log bal
        WHERE bal.type_ops IN (\'deposit_interest_charge\', \'deposit_commision\')
        ';

        if (!empty($month)) {
            $sql.= ' AND MONTH(bal.date_ops) = :month';
            $execute['month'] = $month;
        }

        if (!empty($year)) {
            $sql.= ' AND YEAR(bal.date_ops) = :year';
            $execute['year'] = $year;
        }
        $sql.= '
        GROUP BY YEAR(bal.date_ops), MONTH(bal.date_ops)
        ORDER BY MONTH(bal.date_ops) ASC
        ';
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute($execute);
        } catch (DBALException $e) {
            throw $e;
        }

        return $stmt->fetchAll();
    }
}
