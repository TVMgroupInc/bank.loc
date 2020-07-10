<?php

namespace App\Service;

use App\Entity\BankAccount;
use App\Entity\Client;
use App\Entity\Deposit;
use App\Entity\DepositReplenishmentLog;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class DepositService
 * @package App\Service
 */
class DepositService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function createDepositCase(array $data)
    {
        //Create client. Fill client fields.
        $client = new Client();
        $client->setFirstName('John')
            ->setLastName('Doe')
            ->setDateOfBirth(new \DateTime('1991-05-12'))
            ->setGender(1)
            ->setInn(435434766776);
        //Create bank account. Fill bank account fields.
        $bankAccount = new BankAccount();
        $bankAccount->setCurrency('USD')
            ->setBalance(7000)
            ->setIban('FSF534534636FFFDF')
            ->setClient($client);
        //Create deposit. Fill deposit fields.
        $deposit = new Deposit();
        $deposit->setInterestRate(4.5)
            ->setAccount($bankAccount);
        //Create deposit replenishment log
        $replenishmentLog = new DepositReplenishmentLog();
        $replenishmentLog->setDate($deposit->getDateOpen())
            ->setSum(7000)
            ->setDeposit($deposit);
        //Prepare objects to inser db
        $this->em->persist($client);
        $this->em->persist($bankAccount);
        $this->em->persist($deposit);
        $this->em->persist($replenishmentLog);

        try {
            //insert to db
            $this->em->flush();
            $msg = 'success';
        } catch (\Exception $e) {
            $msg = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
        }

        return $msg;
    }
}