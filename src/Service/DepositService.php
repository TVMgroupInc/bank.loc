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
     * This method create: client, bank account, deposit, deposit replenishment log.
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
        //Create deposit replenishment log
        $replenishmentLog = new DepositReplenishmentLog();
        $replenishmentLog->setDate($deposit->getDateOpen())
            ->setSum($data['balance'])
            ->setDeposit($deposit);

        //Prepare objects to insert db
        $this->em->persist($client);
        $this->em->persist($bankAccount);
        $this->em->persist($deposit);
        $this->em->persist($replenishmentLog);

        try {
            //insert to db
            $this->em->flush();
        } catch (\Exception $e) {
            throw $e;
        }
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

            $iban = $ibanPt1 . $ibanPt2 . $ibanPt3;
        } catch (\Exception $e) {
            throw $e;
        }


        return substr($iban, 0, 34);
    }

    /**
     * Pseudo generate Interest Rate
     * @return float
     */
    public function generateInterestRate():float
    {
        return (float)mt_rand(100, 3000) / 100;
    }
}