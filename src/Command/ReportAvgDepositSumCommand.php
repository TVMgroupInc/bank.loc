<?php

namespace App\Command;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ReportAvgDepositSumCommand extends Command
{
    protected static $defaultName = 'app:report-avg-deposit-sum';
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
            ->setDescription('Table in console for second report avg deposit sum')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $avgSumResRows = [
                '18-25' => $this->em->getRepository('App:Deposit')->getAvgSumDepositForAgeGroup(18, 25),
                '25-50' => $this->em->getRepository('App:Deposit')->getAvgSumDepositForAgeGroup(25, 50),
                '50' => $this->em->getRepository('App:Deposit')->getAvgSumDepositForAgeGroup(50),
            ];
        } catch (\Exception $e) {
            $io->error(sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage()));
            return 0;
        }
        $rows = [];

        foreach ($avgSumResRows as $bbKey => $bbVal) {
            $rows[] = [$bbKey ?? '', $bbVal ?? ''];
        }

        $table = new Table($output);
        $table
            ->setHeaders(['GROUP', 'AVG Sum deposit'])
            ->setRows($rows)
        ;
        $table->render();

        return 0;
    }
}
