<?php

namespace App\Controller\TransactionManagement;

use App\Entity\TransactionManagement\Livraison;
use App\Form\TransactionManagement\LivraisonType;
use App\Form\TransactionManagement\LivraisonPurchaseType;
use App\Repository\TransactionManagement\LivraisonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\TransactionManagement\Purchase;
use App\Entity\TransactionManagement\PurchaseItem;
use App\Entity\LovManagement\Etat;
use App\Repository\LovManagement\EtatRepository;

/**
 * @Route("/transaction/management/livraison")
 */
class LivraisonController extends AbstractController
{
    /**
     * @Route("/", name="transaction_management_livraison_index", methods={"GET","POST"})
     */
    public function index(LivraisonRepository $livraisonRepository,EtatRepository $etatRepository, Request $request): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        foreach ($etatRepository->findAll() as $etat) {
            $etatLivraison[$etat->getTitle()] = $livraisonRepository->findByEtat($etat);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $livraison->setCreateDate(new \Datetime());
            $livraison->setUpdateDate(new \Datetime());
            $entityManager->persist($livraison);
            $entityManager->flush();

            return $this->redirectToRoute('transaction_management_livraison_index', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('transaction_management/livraison/index.html.twig', [
            'livraisons' => $livraisonRepository->findAll(),
            'livraison' => $livraison,
            'etatLivraison' => $etatLivraison,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/{id}", name="transaction_management_livraison_new", methods={"GET","POST"})
     */
    public function newCommande(Request $request, PurchaseItem $purchaseItems): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        //$commande = $this->getDoctrine()->getRepository(Purchase::class)->findByPurchaseItems($purchaseItems);
        dump($purchaseItems);
        if($purchaseItems->getLivraison()){
            if($purchaseItems->getLivraison()->getParent()){
                return $this->redirectToRoute('transaction_management_livraison_index', ['id' => $purchaseItems->getId()]);
            }
        }
        
        $livraison = new Livraison();
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {

            $livraison->setCreateDate(new \Datetime());
            $livraison->setUpdateDate(new \Datetime());
            $livraison->addPurchasedItem($purchaseItems);
            $entityManager->persist($livraison);
            $purchaseItems->setLivraison($livraison);
            $entityManager->persist($purchaseItems);

            if($request->request->get('nouvelle')){
                return $this->redirectToRoute('transaction_management_livraison_index', ['id' => $purchaseItems->getId()]);
            }else{
                $souslivraison = clone $livraison;
                $souslivraison->setParent($livraison);
                $entityManager->persist($souslivraison);
            }
            
            $entityManager->flush();
            return $this->redirectToRoute('transaction_management_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/livraison/nouvelle.html.twig', [
            'livraison' => $livraison,
            'form' => $form->createView(),
            'sousCommande' => $purchaseItems,
            'type' => "parent"
        ]);
    }

    /**
     * @Route("/souscommande/{id}/{parent}", defaults={"parent"=false}, name="sous_livraison_new", methods={"GET","POST"})
     */
    public function newSousCommande(Request $request, PurchaseItem $purchaseItems, $parent): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(SousLivraisonType::class, $livraison);
        $form->handleRequest($request);

        if($parent){
            $livraison = clone $purchaseItems->getLivraison();
            $entityManager->persist($souslivraison);
            $entityManager->flush();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $livraison->setCreateDate(new \Datetime());
            $livraison->setUpdateDate(new \Datetime());
            $livraison->addPurchasedItem($PurchaseItem);
            $souslivraison->setParent($purchaseItems->getLivraison()->getParent());
            $entityManager->persist($livraison);
            $entityManager->flush();
            return $this->redirectToRoute('transaction_management_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/livraison/nouvelle.html.twig', [
            'livraison' => $livraison,
            'sousCommande' => $purchaseItems,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new/souslivraison/{id}", name="livraison_new", methods={"GET","POST"})
     */
    public function newLivraison(Request $request, PurchaseItem $purchaseItem): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $livraison = new Livraison();
        $form = $this->createForm(LivraisonPurchaseType::class, $livraison);
        $form->handleRequest($request);

        $sousLivraisons = $this->getDoctrine()->getRepository(Livraison::class)->findByParent($purchaseItem->getLivraison());

        if ($form->isSubmitted() && $form->isValid()) {
            
            $livraison->addPurchasedItem($purchaseItem);
            $livraison->setParent($purchaseItem->getLivraison());
            $livraison->setCreateDate(new \Datetime());
            $livraison->setUpdateDate(new \Datetime());
            $entityManager->persist($livraison);
            $entityManager->flush();

            return $this->redirectToRoute('transaction_management_purchase_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/livraison/nouvelle.html.twig', [
            'sousCommande' => $purchaseItem,
            'livraison' => $purchaseItem->getLivraison(),
            'sousLivraisons' => $sousLivraisons, 
            'form' => $form->createView(),
        ]);
    }

     /**
     * @Route("/nouvelle/livraison", name="nouvelle_livraison", methods={"GET","POST"})
     */
    public function nouvelleLivraison(Request $request): Response
    {
        $livraison = new Livraison();
        $form = $this->createForm(LivraisonPurchaseType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($livraison);
            $entityManager->flush();

            return $this->redirectToRoute('transaction_management_purchase_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/livraison/new.html.twig', [
            'livraison' => $livraison,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_management_livraison_show", methods={"GET"})
     */
    public function show(Livraison $livraison): Response
    {
        return $this->render('transaction_management/livraison/show.html.twig', [
            'livraison' => $livraison,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transaction_management_livraison_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Livraison $livraison): Response
    {
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_management_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/livraison/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/transporteur", name="choix_transporteur", methods={"GET","POST"})
     */
    public function choixTransporteur(Request $request, Livraison $livraison): Response
    {
        $form = $this->createForm(LivraisonType::class, $livraison);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_management_livraison_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/livraison/edit.html.twig', [
            'livraison' => $livraison,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_management_livraison_delete", methods={"POST"})
     */
    public function delete(Request $request, Livraison $livraison): Response
    {
        if ($this->isCsrfTokenValid('delete'.$livraison->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($livraison);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transaction_management_livraison_index', [], Response::HTTP_SEE_OTHER);
    }
}
