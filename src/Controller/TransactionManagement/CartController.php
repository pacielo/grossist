<?php

namespace App\Controller\TransactionManagement;

use App\Entity\TransactionManagement\Product;
use App\Form\TransactionManagement\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TransactionManagement\CartType;
use App\Model\CartManager;

/**
 * @Route("/transaction/management/cart")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/", name="transaction_management_cart_index", methods={"GET","POST"})
     */
     public function index(Request $request, CartManager $cartManager): Response
    {
        $cart = $cartManager->getCurrentCart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        // if ($form->isSubmitted() && $form->isValid()) {
        if ($form->isSubmitted()) {
            $cart->setBuyer($this->getUser());
            $cart->setUpdatedAt(new \DateTime());
            $cartManager->save($cart);

            if($request->request->get('achat')){   
                // Persist in database
                $entityManager = $this->getDoctrine()->getManager();
                $cart->setStatus('valide');
                $entityManager->persist($cart);
                $entityManager->flush();
            }
            //return $this->redirectToRoute('cart');
        }
            
        return $this->render('transaction_management/cart/index.html.twig', [
            'cart' => $cart,
            'form' => $form->createView()
        ]);
    }

}
