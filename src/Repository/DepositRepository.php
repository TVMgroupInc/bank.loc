<?php

namespace App\Repository;

use App\Entity\Deposit;
use App\Entity\DepositCommissionLog;
use App\Entity\DepositInterestChargeLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Deposit|null find($id, $lockMode = null, $lockVersion = null)
 * @method Deposit|null findOneBy(array $criteria, array $orderBy = null)
 * @method Deposit[]    findAll()
 * @method Deposit[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepositRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Deposit::class);
    }

    // /**
    //  * @return Deposit[] Returns an array of Deposit objects
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
    public function findOneBySomeField($value): ?Deposit
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    /**
     * Get deposits that need to be processed today
     * @param \DateTime $date
     * @return array|null
     * @throws \Exception
     */
    public function getDepositByDateForInterestCharge(\DateTime $date): ?array
    {
        //Count day in month
        $numDayMonth = cal_days_in_month(CAL_GREGORIAN, $date->format('m'), $date->format('Y'));
        $curDay = [$date->format('d')];

        //Validate of last day in month.
        //If 30 add 31 to arr.
        if ($date->format('d') == $numDayMonth && $numDayMonth == 30) {
            $curDay[] = 31;
        }
        //If February and last day(28 or 29) add 30 and 31
        if ($date->format('d') == $numDayMonth && $date->format('m') == 2) {
            $curDay[] = 30;
            $curDay[] = 31;
        }

        //All deposit who create in this day
        $qbDeposit = $this->createQueryBuilder('d');
        $qbDeposit->leftJoin(
            DepositInterestChargeLog::class,
            'dicl',
            'with',
            'dicl.deposit = d.id AND dicl.date >= :midnight_cur_day'
        );
        $qbDeposit->where(
            $qbDeposit->expr()->andX(
                $qbDeposit->expr()->isNull('d.date_close'),
                $qbDeposit->expr()->in('DAY(d.date_open)', ':cur_day'),
                $qbDeposit->expr()->lt('d.date_open', ':midnight_cur_day'),
                $qbDeposit->expr()->isNull('dicl.id')
            )
        );
        $qbDeposit->setParameter('cur_day', $curDay);
        $qbDeposit->setParameter('midnight_cur_day', $date->format('Y-m-d 00:00:00'));
        /* SQL Analog
        SELECT d.id AS deposit_id, bank_account.id AS bank_account_id, d.interest_rate
        FROM deposit d
        INNER JOIN bank_account ON d.account_id = bank_account.id
        LEFT JOIN deposit_interest_charge_log dicl ON dicl.deposit_id = d.id AND dicl.date >= :midnight_cur_day
        WHERE DAY(d.date_open) IN (:cur_day) AND d.date_open < :midnight_cur_day AND dicl.id IS NULL*/

        try {
            $depositsOps = $qbDeposit->getQuery()->getResult();
        } catch (\Exception $e) {
            throw $e;
        }

        return $depositsOps;
    }

    public function getDepositForCommision($date)
    {
        $qbDeposit = $this->createQueryBuilder('d');
        $qbDeposit->leftJoin(
            DepositCommissionLog::class,
            'dcl',
            'with',
            'dcl.deposit = d.id AND dcl.date >= :midnight_cur_day'
        );
        $qbDeposit->where(
            $qbDeposit->expr()->andX(
                $qbDeposit->expr()->isNull('d.date_close'),
                $qbDeposit->expr()->lt('d.date_open', ':midnight_cur_day'),
                $qbDeposit->expr()->isNull('dcl.id')
            )
        );
        $qbDeposit->setParameter('midnight_cur_day', $date->format('Y-m-01 00:00:00'));
        /* SQL Analog
        SELECT *
        FROM db_bank.deposit d
        LEFT JOIN db_bank.deposit_commission_log dcl ON dcl.deposit_id = d.id AND dcl.date >= :midnight_cur_day
        WHERE d.date_close IS NULL AND d.date_open < :midnight_cur_day AND dcl.id IS NULL  */

        try {
            $depositsOps = $qbDeposit->getQuery()->getResult();
        } catch (\Exception $e) {
            throw $e;
        }

        return $depositsOps;
    }
}
