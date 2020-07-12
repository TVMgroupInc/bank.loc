<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class ReportController extends AbstractController
{
    /**
     * @Route("/report/bank-balance", name="report_bank_balance")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function bankBalanceByMonthAction(EntityManagerInterface $em)
    {
        try {
            $bankBalanceResRows = $em->getRepository('App:BankAccountLog')->getBankBalanceByMonth();
        } catch (\Exception $e) {
            $bankBalanceResRows = [];
            $this->addFlash('error', sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage()));
        }

        return $this->render('report/bank_balance.html.twig', [
            'bankBalanceResRows' => $bankBalanceResRows
        ]);
    }

    /**
     * @Route("/report/avg-sum-deposit", name="report_avg_sum_deposit")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function avgSumDepositForAgeGroupAction(EntityManagerInterface $em)
    {
        try {
            $avgSumResRows = [
                '18-25' => $em->getRepository('App:Deposit')->getAvgSumDepositForAgeGroup(18, 25),
                '25-50' => $em->getRepository('App:Deposit')->getAvgSumDepositForAgeGroup(25, 50),
                '50' => $em->getRepository('App:Deposit')->getAvgSumDepositForAgeGroup(50),
            ];
        } catch (\Exception $e) {
            $avgSumResRows = [];
            $this->addFlash('error', sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage()));
        }

        return $this->render('report/avg_sum_deposit_for_age_group.html.twig', [
            'avgSumResRows' => $avgSumResRows
        ]);
    }

}
