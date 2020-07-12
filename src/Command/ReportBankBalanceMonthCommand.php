<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReportBankBalanceMonthCommand extends Command
{
    protected static $defaultName = 'app:report-bank-balance-month';
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * DepositCommissionCommand constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Table in console for first report bank balance by month')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $bankBalanceResRows = $this->em->getRepository('App:BankAccountLog')->getBankBalanceByMonth();
        } catch (\Exception $e) {
            $io->error(sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage()));
            return 0;
        }
        $rows = [];

        foreach ($bankBalanceResRows as $bbKey => $bbVal) {
            $rows[] = [$bbVal['year_month_period'] ?? '', $bbVal['bank_balance'] ?? ''];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['YEAR-MONTH', 'Balance'])
            ->setRows($rows)
        ;
        $table->render();

        return 0;
    }
}
