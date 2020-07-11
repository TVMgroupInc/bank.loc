<?php

namespace App\Command;

use App\Entity\BankAccountLog;
use App\Entity\DepositInterestChargeLog;
use App\Service\DepositService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use function Doctrine\ORM\QueryBuilder;

class DepositInterestChargeCommand extends Command
{
    protected static $defaultName = 'app:deposit-interest-charge';
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var DepositService
     */
    private $depositService;

    /**
     * DepositInterestChargeCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(
        EntityManagerInterface $em,
        DepositService $depositService
    ) {
        $this->em = $em;
        $emConfig = $this->em->getConfiguration();
        $emConfig->addCustomDatetimeFunction('DAY', 'DoctrineExtensions\Query\Mysql\Day');
        $this->depositService = $depositService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('This command checks whether you need to accrue interest on the deposit on this day.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $todayDate = new \DateTime();

        try {
            $depositsOps = $this->em->getRepository('App:Deposit')->getDepositByDate($todayDate);
        } catch (\Exception $e) {
            $io->error(sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage()));

            return 0;
        }

        if (empty($depositsOps)) {
            $io->success('No tasks to complete operations');

            return 0;
        }

        foreach ($depositsOps as $dKey => $deposit) {
            try {
                $this->depositService->makeInterestDeposit($deposit, $todayDate);
                $io->success("Interest on deposit id: {$deposit->getId()} calculated successfully");
            } catch (\Exception $e) {
                $io->error(sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage()));
                $depositErr[] = $deposit;
                continue;
            }
        }

        //If there are unsuccessful attempts. Need to write to the log or send to mail
        if (!empty($depositErr)) {
            $io->warning(var_export($depositErr));
        }

        $io->success("All operations completed successfully.");

        return 0;
    }
}
