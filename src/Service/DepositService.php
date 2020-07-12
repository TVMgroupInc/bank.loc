<?php

namespace App\Service;

use App\Entity\BankAccount;
use App\Entity\BankAccountLog;
use App\Entity\Client;
use App\Entity\Deposit;
use App\Entity\DepositCommissionLog;
use App\Entity\DepositInterestChargeLog;
use App\Entity\DepositReplenishmentLog;
use App\Exception\BankAccount\BankAccountNegativeBalanceException;
use App\Exception\Deposit\DepositCommissionAlreadyException;
use App\Exception\Deposit\DepositInterestAlreadyException;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DepositService
 * @package App\Service
 */
class DepositService
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DepositService constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * This method create: client, bank account, bank account log, deposit, deposit replenishment log.
     * @param array $data
     * @throws \Exception
     */
    public function createDepositCase(array $data): void
    {
        if (empty($data)) {
            return;
        }
        //Create client. Fill client fields.
        $client = new Client();
        $client->setFirstName($data['first_name'])
            ->setLastName($data['last_name'])
            ->setDateOfBirth(new \DateTime($data['date_of_birth']))
            ->setGender($data['gender'])
            ->setInn($data['inn']);
        //Create bank account. Fill bank account fields.
        $bankAccount = new BankAccount();
        $bankAccount->setCurrency($data['currency'])
            ->setBalance($data['balance'])
            ->setIban($data['iban'])
            ->setClient($client);
        //Create deposit. Fill deposit fields.
        $deposit = new Deposit();
        $deposit->setInterestRate($data['interest_rate'])
            ->setAccount($bankAccount);
        //Create bank account log
        $bankAccountLog = new BankAccountLog();
        $bankAccountLog->setBalanceChange($data['balance'])
            ->setDateOps($deposit->getDateOpen())
            ->setTypeOps('deposit_replenishment')
            ->setBankAccount($bankAccount);
        //Create deposit replenishment log
        $replenishmentLog = new DepositReplenishmentLog();
        $replenishmentLog->setDate($deposit->getDateOpen())
            ->setSum($data['balance'])
            ->setDeposit($deposit);

        //Prepare objects to insert db
        $this->em->persist($client);
        $this->em->persist($bankAccount);
        $this->em->persist($deposit);
        $this->em->persist($bankAccountLog);
        $this->em->persist($replenishmentLog);

        try {
            //insert to db
            $this->em->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Make interest on a deposit for a certain date
     * @param Deposit $deposit
     * @param \DateTime $date
     * @throws \Exception
     */
    public function makeInterestDeposit(Deposit $deposit, \DateTimeInterface $dateOps): void
    {
        try {
            //Get bank account(not proxy).
            $bankAccount = $this->em->find('App:BankAccount', $deposit->getAccount()->getId());

            if ($bankAccount->getBalance() <= 0) {
                throw new BankAccountNegativeBalanceException('Negative or zero account balance detected');
            }

            if (!$this->checkMakeInterestDeposit($deposit->getId(), $dateOps)) {
                throw new DepositInterestAlreadyException('This month, interest has been accrued on the deposit');
            }
            $interestSum = $bankAccount->getBalance() * ((float)$deposit->getInterestRate() / 100);
            //$interestSum = round($interestSum, 2);//Probably in a real bank
            //Bank account update balance
            $bankAccount->setBalance($bankAccount->getBalance() + $interestSum);

            //Create bank account log
            $bankAccountLog = new BankAccountLog();
            $bankAccountLog->setBalanceChange($interestSum)
                ->setDateOps($dateOps)
                ->setTypeOps('deposit_interest_charge')
                ->setBankAccount($bankAccount);

            //Create deposit interest charge log
            $depositInterestChargeLog = new DepositInterestChargeLog();
            $depositInterestChargeLog->setDate($dateOps)
                ->setSum($interestSum)
                ->setDeposit($deposit);

            //Prepare object to insert
            $this->em->persist($bankAccountLog);
            $this->em->persist($depositInterestChargeLog);
            $this->em->persist($bankAccount);

            //Insert to db
            $this->em->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checking whether the deposit needs to accrue interest
     * @param \DateTimeInterface $dateOps
     * @param int|null $depositId
     * @return bool
     * @throws \Exception
     */
    private function checkMakeInterestDeposit(int $depositId, \DateTimeInterface $dateOps): bool
    {
        try {
            $depositInfo = $this->em->getRepository('App:Deposit')
                ->getDepositByDateForInterestCharge($dateOps, $depositId);
        } catch (\Exception $e) {
            throw $e;
        }

        return !empty($depositInfo);
    }


    /**
     * Make commision on a deposit for a certain date
     * @param Deposit $deposit
     * @param \DateTimeInterface $dateOps
     * @throws \Exception
     */
    public function makeCommissionDeposit(Deposit $deposit, \DateTimeInterface $dateOps): void
    {
        try {
            //Get bank account(not proxy).
            $bankAccount = $this->em->find('App:BankAccount', $deposit->getAccount()->getId());

            if ($bankAccount->getBalance() <= 0) {
                throw new BankAccountNegativeBalanceException('Negative or zero account balance detected');
            }

            //Making the commission the first number month
            if ($dateOps->format('d') != 1) {
                $dateOps->modify('first day of this month');
            }

            if (!$this->checkMakeCommissionDeposit($deposit->getId(), $dateOps)) {
                throw new DepositCommissionAlreadyException('This month, the withdrawal of the commission was already');
            }
            //Commision sum and percent
            $commision = $this->commissionCalc($bankAccount->getBalance(), $deposit->getDateOpen(), $dateOps);

            //Bank account update balance
            $bankAccount->setBalance($bankAccount->getBalance() - $commision['sum']);

            //Create bank account log
            $bankAccountLog = new BankAccountLog();
            $bankAccountLog->setBalanceChange($commision['sum'] * -1)
                ->setDateOps($dateOps)
                ->setTypeOps('deposit_commision')
                ->setBankAccount($bankAccount);

            //Create deposit interest charge log
            $depositCommisionLog = new DepositCommissionLog();
            $depositCommisionLog->setDate($dateOps)
                ->setSum($commision['sum'])
                ->setPercent($commision['percent'])
                ->setDeposit($deposit);

            //Prepare object to insert
            $this->em->persist($bankAccountLog);
            $this->em->persist($depositCommisionLog);
            $this->em->persist($bankAccount);

            //Insert to db
            $this->em->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Checking whether the deposit needs to commission
     * @param int $depositId
     * @param \DateTimeInterface $dateOps
     * @return bool
     * @throws \Exception
     */
    private function checkMakeCommissionDeposit(int $depositId, \DateTimeInterface $dateOps): bool
    {
        try {
            $depositInfo = $this->em->getRepository('App:Deposit')->getDepositForCommision($dateOps, $depositId);
        } catch (\Exception $e) {
            throw $e;
        }

        return !empty($depositInfo);
    }

    /**
     * Pseudo generate IBAN
     * @return string
     * @throws \Exception
     */
    public function generateIBAN(): string
    {
        try {
            $ibanPt1 = md5(((new \DateTime())->getTimestamp()) . uniqid(rand(), true)  . rand(0, 1000000));
            $ibanPt2 = bin2hex(random_bytes(22));
            $ibanPt3 = base64_encode(openssl_random_pseudo_bytes(32));
        } catch (\Exception $e) {
            throw $e;
        }
        
        return substr($ibanPt1 . $ibanPt2 . $ibanPt3, 0, 34);
    }

    /**
     * Pseudo generate Interest Rate
     * @return float
     */
    public function generateInterestRate():float
    {
        return (float)mt_rand(100, 3000) / 100;
    }

    /**
     * Calculate commission sum and percent. Return arr with sum and percent
     * @param float $balance
     * @param \DateTimeInterface $depositOpen
     * @param \DateTimeInterface $dateOps
     * @return array|float[]|int[]
     * @throws BankAccountNegativeBalanceException
     */
    private function commissionCalc(
        float $balance,
        \DateTimeInterface $depositOpen,
        \DateTimeInterface $dateOps
    ): array {
        if ($balance <= 0) {
            throw new BankAccountNegativeBalanceException('Negative or zero account balance detected');
        }
        //We assign a percentage depending on the balance.
        // 0..1000 = 5; 1000..10000 = 6; > 10000 = 7
        $cmmssnPercent = $balance >= 10000 ? 7 : ($balance >= 1000 && $balance < 10000 ? 6 : 5);
        //We need the previous month to check the date of deposit creation
        $prevMonth = (clone $dateOps)->modify('-1 month');

        //If deposit create in prev month calc percent with special formula.
        //Standart percent * diff count day with create date and date ops / count day in month when create deposit
        if ($depositOpen > $prevMonth) {
            $cmmssnPercent = $cmmssnPercent
                * $depositOpen->diff($dateOps)->format('%a')
                / cal_days_in_month(CAL_GREGORIAN, $depositOpen->format('m'), $depositOpen->format('Y'))
            ;
            $cmmssnPercent = round($cmmssnPercent, 2);
            //First commision flag
            $fc = 1;
        }
        //Calc commision sum
        $cmmssnSum = $balance * ((float)$cmmssnPercent / 100);
        //Min and Max sum commision if not first commision. Min 50, Max: 5000
        $cmmssnSum = !isset($fc) && $cmmssnSum < 50 ? 50 : (!isset($fc) && $cmmssnSum > 5000 ? 5000 : $cmmssnSum);

        return [
            'sum' => $cmmssnSum,
            'percent' => $cmmssnPercent
        ];
    }
}