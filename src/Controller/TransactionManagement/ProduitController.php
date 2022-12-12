<?php

namespace App\Controller\TransactionManagement;

use App\Entity\TransactionManagement\Product;
use App\Form\TransactionManagement\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TransactionManagement\AddToCartType;
use App\Repository\TransactionManagement\CategoryRepository;
use App\Repository\TransactionManagement\ProductRepository;
use App\Model\CartManager;

/**
 * @Route("/produit")
 */
class ProduitController extends AbstractController
{
    /**
     * @Route("/{cat}", name="produits", defaults={"cat": ""},  methods={"GET","POST"})
     */
    public function index(Request $request,ProductRepository $productRepository, CategoryRepository $categoryRepository, string $cat = NULL ): Response
    {
        $categorie = $categoryRepository->find($cat);
        $products = $productRepository->findAll();

        $cate = null;
        if($categorie){
            $products = $productRepository->getCat($categorie->getId());
            $cate = $categorie->getTitle();
        }

        if($request->get('recherche')){
            $products = $productRepository->getByName($request->get('recherche'), $cate);
        }

        return $this->render('transaction_management/product/liste.html.twig', [
            'products' => $products,
            'categories' => $categoryRepository->findAll(),
            'cat' => $categorie
        ]);
    }


    /**
     * @Route("/show/{id}", name="transaction_management_product_show", methods={"GET","POST"})
     */
    public function show(Product $product, Request $request, CartManager $cartManager)
    {
         $form = $this->createForm(AddToCartType::class);
         $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            $cart->addPurchasedItems($item)->setUpdatedAt(new \DateTime());
            

            $cartManager->save($cart);
            return $this->redirectToRoute('transaction_management_cart_index');
            //return $this->redirectToRoute('transaction_management_product_show', ['id' => $product->getId()]);
        }


        return $this->render('transaction_management/product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

}
