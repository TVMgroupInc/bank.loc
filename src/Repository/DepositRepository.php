<?php

namespace App\Repository;

use App\Entity\Deposit;
use App\Entity\DepositCommissionLog;
use App\Entity\DepositInterestChargeLog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\DBALException;
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
     * Get deposits that need a interest charge
     * @param \DateTimeInterface $dateOps
     * @param int|null $depositId
     * @return array|null
     * @throws \Exception
     */
    public function getDepositByDateForInterestCharge(\DateTimeInterface $dateOps, int $depositId = null): ?array
    {
        //Count day in month
        $numDayMonth = cal_days_in_month(CAL_GREGORIAN, $dateOps->format('m'), $dateOps->format('Y'));
        $curDay = [$dateOps->format('d')];

        //Validate of last day in month.
        //If 30 add 31 to arr.
        if ($dateOps->format('d') == $numDayMonth && $numDayMonth == 30) {
            $curDay[] = 31;
        }
        //If February and last day(28 or 29) add 30 and 31
        if ($dateOps->format('d') == $numDayMonth && $dateOps->format('m') == 2) {
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

        //If need check by deposit id
        if (!empty($depositId)) {
            $qbDeposit->andWhere($qbDeposit->expr()->eq('d.id', ':deposit_id'));
            $qbDeposit->setParameter('deposit_id', $depositId);
        }
        $qbDeposit->setParameter('cur_day', $curDay);
        $qbDeposit->setParameter('midnight_cur_day', $dateOps->format('Y-m-d 00:00:00'));
        /* SQL Analog
        SELECT *
        FROM deposit d
            INNER JOIN bank_account ON d.account_id = bank_account.id
            LEFT JOIN deposit_interest_charge_log dicl ON dicl.deposit_id = d.id AND dicl.date >= :midnight_cur_day
        WHERE d.date_close IS NULL
            AND DAY(d.date_open) IN (:cur_day)
            AND d.date_open < :midnight_cur_day
            AND dicl.id IS NULL
            AND d.id = :deposit_id(optional)*/

        try {
            $depositsOps = $qbDeposit->getQuery()->getResult();
        } catch (\Exception $e) {
            throw $e;
        }

        return $depositsOps;
    }

    /**
     * Get deposits that need a commission
     * @param \DateTimeInterface $dateOps
     * @return array|null
     * @throws \Exception
     */
    public function getDepositForCommision(\DateTimeInterface $dateOps, int $depositId = null): ?array
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

        //If need check by deposit id
        if (!empty($depositId)) {
            $qbDeposit->andWhere($qbDeposit->expr()->eq('d.id', ':deposit_id'));
            $qbDeposit->setParameter('deposit_id', $depositId);
        }
        $qbDeposit->setParameter('midnight_cur_day', $dateOps->format('Y-m-01 00:00:00'));
        /* SQL Analog
        SELECT *
        FROM db_bank.deposit d
            LEFT JOIN db_bank.deposit_commission_log dcl ON dcl.deposit_id = d.id AND dcl.date >= :midnight_cur_day
        WHERE d.date_close IS NULL
            AND d.date_open < :midnight_cur_day
            AND dcl.id IS NULL
            AND d.id = :deposit_id(optional) */

        try {
            $depositsOps = $qbDeposit->getQuery()->getResult();
        } catch (\Exception $e) {
            throw $e;
        }

        return $depositsOps;
    }

    /**
     * Average deposit amount (Amount of deposits / Number of deposits) for age groups
     * @param int $ageFrom
     * @param int|null $ageTo
     * @return string|null
     * @throws DBALException
     */
    public function getAvgSumDepositForAgeGroup(int $ageFrom, int $ageTo = null): ?string
    {
        $conn = $this->getEntityManager()->getConnection();
        $ageFrom = (new \DateTime())->modify("- {$ageFrom} year")->format('Y-m-d');

        $sql = '
        SELECT ROUND(SUM(ba.balance) / COUNT(d.id), 2) AS avg_amount
        FROM db_bank.client c
            INNER JOIN db_bank.bank_account ba ON c.id = ba.client_id
            INNER JOIN db_bank.deposit d ON ba.id = d.account_id
        WHERE c.date_of_birth <= :ageFrom;
        ';
        $execute = ['ageFrom' => $ageFrom];

        if (!empty($ageTo)) {
            //People who have not yet turned 25 for example, but this year they will be 25
            $sql.= ' AND c.date_of_birth > :ageTo';
            $execute['ageTo'] = (new \DateTime())->modify("- {$ageTo} year")->format('Y-m-d');
        }
        $stmt = $conn->prepare($sql);

        try {
            $stmt->execute($execute);
        } catch (DBALException $e) {
            throw $e;
        }

        return $stmt->fetchColumn();
    }
}
