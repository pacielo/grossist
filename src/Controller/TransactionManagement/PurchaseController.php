<?php

namespace App\Controller\TransactionManagement;

use App\Entity\TransactionManagement\Purchase;
use App\Form\TransactionManagement\PurchaseType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transaction/management/purchase")
 */
class PurchaseController extends AbstractController
{
    /**
     * @Route("/", name="transaction_management_purchase_index", methods={"GET"})
     */
    public function index(): Response
    {
        $purchases = $this->getDoctrine()
            ->getRepository(Purchase::class)
            ->findAll();

        return $this->render('transaction_management/purchase/index.html.twig', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * @Route("/new", name="transaction_management_purchase_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $purchase = new Purchase();
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($purchase);
            $entityManager->flush();

            return $this->redirectToRoute('transaction_management_purchase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/purchase/new.html.twig', [
            'purchase' => $purchase,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_management_purchase_show", methods={"GET"})
     */
    public function show(Purchase $purchase): Response
    {
        return $this->render('transaction_management/purchase/show.html.twig', [
            'purchase' => $purchase,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transaction_management_purchase_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Purchase $purchase): Response
    {
        $form = $this->createForm(PurchaseType::class, $purchase);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_management_purchase_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/purchase/edit.html.twig', [
            'purchase' => $purchase,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_management_purchase_delete", methods={"POST"})
     */
    public function delete(Request $request, Purchase $purchase): Response
    {
        if ($this->isCsrfTokenValid('delete'.$purchase->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchase);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transaction_management_purchase_index', [], Response::HTTP_SEE_OTHER);
    }
}
