<?php

namespace App\Command;

use App\Service\DepositService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

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
     * @param DepositService $depositService
     */
    public function __construct(
        EntityManagerInterface $em,
        DepositService $depositService
    ) {
        $this->em = $em;
        $this->depositService = $depositService;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('This command checks whether you need to accrue interest on the deposit on this day.')
            ->addOption(
                'date',
                null,
                InputOption::VALUE_REQUIRED,
                'Enter the date for which you want to make operations. Format Y-m-d',
                null
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $dateOption = $input->getOption('date');
        $dateOps = new \DateTime();

        /* start validate date option */
        if (!empty($dateOption) && \DateTime::createFromFormat('Y-m-d', $dateOption) !== false) {
            $dateOps = \DateTime::createFromFormat('Y-m-d', $dateOption);
        }

        try {
            $depositsOps = $this->em->getRepository('App:Deposit')->getDepositByDateForInterestCharge($dateOps);
        } catch (\Exception $e) {
            $io->error(sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage()));

            return 0;
        }

        if (empty($depositsOps)) {
            $io->writeln('No tasks to complete operations');

            return 0;
        }

        foreach ($depositsOps as $dKey => $deposit) {
            try {
                $this->depositService->makeInterestDeposit($deposit, $dateOps);
                $io->success("Interest on deposit id: {$deposit->getId()} calculated successfully");
            } catch (\Exception $e) {
                $io->error(sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage()));
                $depositErr[] = $deposit->getId();
                continue;
            }
        }

        //If there are unsuccessful attempts. Need to write to the log or send to mail
        if (!empty($depositErr)) {
            $io->warning($depositErr);
        }

        return 0;
    }
}
