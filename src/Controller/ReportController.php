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
    public function getBankBalanceByMonthAction(EntityManagerInterface $em)
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

}
