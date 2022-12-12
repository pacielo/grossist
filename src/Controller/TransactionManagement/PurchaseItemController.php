<?php

namespace App\Controller\TransactionManagement;

use App\Entity\TransactionManagement\PurchaseItem;
use App\Entity\TransactionManagement\Purchase;
use App\Form\TransactionManagement\PurchaseItemType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transaction/management/item/purchase")
 */
class PurchaseItemController extends AbstractController
{
    /**
     * @Route("/", name="transaction_management_purchase_item_index", methods={"GET"})
     */
    public function index(): Response
    {
        $purchaseItems = $this->getDoctrine()
            ->getRepository(PurchaseItem::class)
            ->findAll();

        return $this->render('transaction_management/purchase_item/index.html.twig', [
            'purchase_items' => $purchaseItems,
        ]);
    }

    /**
     * @Route("/purchase/{id}", name="purchase_item_index", methods={"GET"})
     */
    public function indexId(Purchase $purchase): Response
    {
        $purchaseItems = $this->getDoctrine()
            ->getRepository(PurchaseItem::class)
            ->findByPurchase($purchase);

        return $this->render('transaction_management/purchase_item/index.html.twig', [
            'purchase_items' => $purchaseItems,
        ]);
    }

    /**
     * @Route("/new", name="transaction_management_purchase_item_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $purchaseItem = new PurchaseItem();
        $form = $this->createForm(PurchaseItemType::class, $purchaseItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($purchaseItem);
            $entityManager->flush();

            return $this->redirectToRoute('transaction_management_purchase_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/purchase_item/new.html.twig', [
            'purchase_item' => $purchaseItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_management_purchase_item_show", methods={"GET"})
     */
    public function show(PurchaseItem $purchaseItem): Response
    {
        return $this->render('transaction_management/purchase_item/show.html.twig', [
            'purchase_item' => $purchaseItem,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transaction_management_purchase_item_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, PurchaseItem $purchaseItem): Response
    {
        $form = $this->createForm(PurchaseItemType::class, $purchaseItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_management_purchase_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/purchase_item/edit.html.twig', [
            'purchase_item' => $purchaseItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_management_purchase_item_delete", methods={"POST"})
     */
    public function delete(Request $request, PurchaseItem $purchaseItem): Response
    {
        if ($this->isCsrfTokenValid('delete'.$purchaseItem->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($purchaseItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transaction_management_purchase_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
