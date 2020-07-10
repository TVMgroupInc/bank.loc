<?php

namespace App\Controller;

use App\Entity\DepositReplenishmentLog;
use App\Service\DepositService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(DepositService $depositService)
    {
        //$m = $depositService->createDeposit();
        $em = $this->getDoctrine()->getManager();

/*        $deposit = $em->find('App:Deposit', 1);
        $replenishmentLog = new DepositReplenishmentLog();
        $replenishmentLog->setDate($deposit->getDateOpen())
            ->setSum(7000)
            ->setDeposit($deposit);

        $em->persist($replenishmentLog);
        $em->flush();*/

        return $this->render('base/index.html.twig', [
            'controller_name' => 'BaseController',
        ]);
    }
}
