<?php

namespace App\DataFixtures;

use App\Entity\BankAccount;
use App\Entity\Client;
use App\Entity\Deposit;
use App\Entity\DepositReplenishmentLog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
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
            ->setDateOpen(new \DateTime('2020-06-30'))
            ->setAccount($bankAccount);
        //Create deposit replenishment log
        $replenishmentLog = new DepositReplenishmentLog();
        $replenishmentLog->setDate($deposit->getDateOpen())
            ->setSum(7000)
            ->setDeposit($deposit);
        //Prepare objects to insert db
        $manager->persist($client);
        $manager->persist($bankAccount);
        $manager->persist($deposit);
        $manager->persist($replenishmentLog);
        //insert to db
        $manager->flush();

        //Create client. Fill client fields.
        $client = new Client();
        $client->setFirstName('Alina')
            ->setLastName('Grosu')
            ->setDateOfBirth(new \DateTime('1988-08-02'))
            ->setGender(0)
            ->setInn(115439966777);
        //Create bank account. Fill bank account fields.
        $bankAccount = new BankAccount();
        $bankAccount->setCurrency('EUR')
            ->setBalance(25000)
            ->setIban('DSFSFSF7732323DJSVV')
            ->setClient($client);
        //Create deposit. Fill deposit fields.
        $deposit = new Deposit();
        $deposit->setInterestRate(7.2)
            ->setDateOpen(new \DateTime('2020-05-21'))
            ->setAccount($bankAccount);
        //Create deposit replenishment log
        $replenishmentLog = new DepositReplenishmentLog();
        $replenishmentLog->setDate($deposit->getDateOpen())
            ->setSum(25000)
            ->setDeposit($deposit);
        //Prepare objects to insert db
        $manager->persist($client);
        $manager->persist($bankAccount);
        $manager->persist($deposit);
        $manager->persist($replenishmentLog);
        //insert to db
        $manager->flush();
    }
}
