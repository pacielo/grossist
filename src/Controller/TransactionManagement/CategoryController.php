<?php

namespace App\Controller\TransactionManagement;

use App\Entity\TransactionManagement\Category;
use App\Form\TransactionManagement\CategoryType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transaction/management/category")
 */
class CategoryController extends AbstractController
{
    /**
     * @Route("/", name="transaction_management_category_index", methods={"GET"})
     */
    public function index(): Response
    {
        $categories = $this->getDoctrine()
            ->getRepository(Category::class)
            ->findAll();

        return $this->render('transaction_management/category/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    /**
     * @Route("/new", name="transaction_management_category_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute(' admin_lov_list', [], Response::HTTP_SEE_OTHER);
        }

        
        $listValue = $em->getRepository("App\Entity\TransactionManagement\Category")->findAll();
        $lovListe = [];
        foreach ($listValue as $value) {
            $lovListe[] = ['val' => $value, 'nb' => 0];
        }

        //return $this->render('transaction_management/category/new.html.twig', [
        return $this->render('LovManagement/lov_add.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
            'entity' => 'Category',
            'lov' => $lovListe,
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_management_category_show", methods={"GET"})
     */
    public function show(Category $category): Response
    {
        return $this->render('transaction_management/category/show.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transaction_management_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transaction_management_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transaction_management/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transaction_management_category_delete", methods={"POST"})
     */
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($category);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transaction_management_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
