<?php

namespace App\Controller;

use App\Form\DepositCaseType;
use App\Form\NewDepositForClientType;
use App\Service\DepositService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DepositController extends AbstractController
{
    /**
     * @Route("/deposit/new", name="deposit_new")
     * @param Request $request
     * @param DepositService $depositService
     * @param FormFactoryInterface $formFactory
     * @return RedirectResponse|Response
     */
    public function depositNew(
        Request $request,
        DepositService $depositService,
        FormFactoryInterface $formFactory
    ) {
        $formDepositCaseType = $formFactory->create(DepositCaseType::class);
        $formDepositCaseType->handleRequest($request);

        if ($formDepositCaseType->isSubmitted() && $formDepositCaseType->isValid()) {
            try {
                $formData = $formDepositCaseType->getData();
                $formData['interest_rate'] = $depositService->generateInterestRate();
                $formData['iban'] = $depositService->generateIBAN();
                $depositService->createDepositCase($formData);
                $msgType = 'success';
                $msg = 'Success create deposit case';
            } catch (\Exception $e) {
                $msgType = 'error';
                $msg = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
            }
            $this->addFlash($msgType, $msg);

            return $this->redirectToRoute('deposit_new');
        }

        return $this->render('deposit/index.html.twig', [
            'controller_name' => 'DepositController',
            'formDepositCaseType' => $formDepositCaseType->createView()
        ]);
    }

    /**
     * @Route("/deposit/add", name="deposit_add")
     * @param Request $request
     * @param DepositService $depositService
     * @param FormFactoryInterface $formFactory
     * @return RedirectResponse|Response
     */
    public function depositAdd(
        Request $request,
        DepositService $depositService,
        FormFactoryInterface $formFactory
    ) {
        $formNewDepositForClientType = $formFactory->create(NewDepositForClientType::class);
        $formNewDepositForClientType->handleRequest($request);

        if ($formNewDepositForClientType->isSubmitted() && $formNewDepositForClientType->isValid()) {
            try {
                $formData = $formNewDepositForClientType->getData();
                $formData['interest_rate'] = $depositService->generateInterestRate();
                $formData['iban'] = $depositService->generateIBAN();
                $depositService->addDepositClient($formData);
                $msgType = 'success';
                $msg = 'Success create deposit case';
            } catch (\Exception $e) {
                $msgType = 'error';
                $msg = sprintf('Exception [%i]: %s', $e->getCode(), $e->getMessage());
            }
            $this->addFlash($msgType, $msg);

            return $this->redirectToRoute('deposit_add');
        }

        return $this->render('deposit/deposit_add.html.twig', [
            'controller_name' => 'DepositController',
            'formNewDepositForClientType' => $formNewDepositForClientType->createView()
        ]);
    }
}
