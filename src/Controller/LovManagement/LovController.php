<?php

namespace App\Controller\LovManagement;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * LovController
 */
class LovController extends AbstractController
{
    /**
     * Affiche la liste dune LOV (List Of Values) du projet.
     * @Route("/admin/lov/list/{entity}", name="admin_lov_list", methods={"GET","HEAD"})
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function list($entity = null)
    {
        $em = $this->getDoctrine()->getManager();
        if ($entity == null || $entity == 'Category') {
            $entity = 'Category';
            $listValue = $em->getRepository("App\Entity\TransactionManagement\\" . $entity)->findAll();
        }else
            $listValue = $em->getRepository("App\Entity\LovManagement\\" . $entity)->findAll();

        $lovListe = [];
        foreach ($listValue as $value) {
            //$nbUsed = $em->getRepository("App\Entity\LovManagement\\" . $entity)->getCountUse($value);
            $lovListe[] = ['val' => $value, 'nb' => 0];
        }

        return $this->render('LovManagement/lov_list.html.twig', [
                'lov' => $lovListe,
                'entity' => $entity,
        ]);
    }

    /**
     * Affiche le formulaire modification d'une LOV (List Of Values) du projet.
     * @Route("/admin/lov/edit/{entity}/{id}", name="admin_lov_edit", methods={"GET","POST"})
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function edit(Request $request, $entity, $id, TranslatorInterface $translator)
    {
        if ($entity == 'Category')
            return $this->redirectToRoute('transaction_management_category_edit',['id' => $id]);

        $em = $this->getDoctrine()->getManager();
        $value = $em->getRepository("App\Entity\LovManagement\\" . $entity)->findOneBy(['id' => $id]);
        $form = $this->createForm("App\Form\LovManagement\LovType", $value);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->getUser();
                $value->setUpdateUser($user);
                $revision = $value->getRevision() + 1;
                $value->setRevision($revision);
                $value->setUpdateDate(new \Datetime());

                try {
                    $em->persist($value);
                    $em->flush();
                    $this->addFlash('success', $translator->trans('lov.actions.flash.updated'));

                    return $this->redirect($this->generateUrl('admin_lov_list', [
                        'entity' => $entity,
                    ]));
                } catch (\Doctrine\DBAL\DBALException $exception) {
                    $this->addFlash('error', $exception->getMessage());
                }
            }
        }
        $listValue = $em->getRepository("App\Entity\LovManagement\\" . $entity)->findAll();

        $lovListe = [];
        foreach ($listValue as $value) {
            //$nbUsed = $em->getRepository("App\Entity\LovManagement\\" . $entity)->getCountUse($value);
            $lovListe[] = ['val' => $value, 'nb' => 0];
        }

        return $this->render('LovManagement/lov_edit.html.twig', [
                'value' => $value,
                'form' => $form->createView(),
                'entity' => $entity,
                'lov' => $lovListe
        ]);
    }

    /**
     * Affiche le formulaire d'ajout d'une LOV (List Of Values) du projet.
     * @Route("/admin/lov/add/{entity}", name="admin_lov_add", methods={"GET","POST"})
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function add(Request $request, $entity, TranslatorInterface $translator)
    {   
        if($entity == 'Category')
            return $this->redirectToRoute('transaction_management_category_new');     

        $em = $this->getDoctrine()->getManager();
        $namespaceEntity = $em->getRepository("App\Entity\LovManagement\\" . $entity)->getClassName();
        $ordre = sizeof($em->getRepository("App\Entity\LovManagement\\" . $entity)->findAll());

        $value = new $namespaceEntity();
        $value->setSort($ordre);
        dump($entity);
        $form = $this->createForm("App\Form\LovManagement\LovType", $value, array('nom' => $entity,));

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $user = $this->getUser();
                $value->setCreateUser($user);
                $value->setUpdateUser($user);
                $value->setRevision(0);
                $value->setCreateDate(new \Datetime());
                $value->setUpdateDate(new \Datetime());
                try {
                    $em->persist($value);
                    $em->flush();

                    $this->addFlash('success', $translator->trans('lov.actions.flash.created'));

                    return $this->redirect($this->generateUrl('admin_lov_list', [
                            'entity' => $entity,
                        ]));
                } catch (\Doctrine\DBAL\DBALException $exception) {
                    $this->addFlash('danger', $exception->getMessage());
                }
            }
        }

        $listValue = $em->getRepository("App\Entity\LovManagement\\" . $entity)->findAll();

        $lovListe = [];
        foreach ($listValue as $value) {
            $lovListe[] = ['val' => $value, 'nb' => 0];
        }

        return $this->render('LovManagement/lov_add.html.twig', [
                'value' => $value,
                'form' => $form->createView(),
                'entity' => $entity,
                'lov' => $lovListe
                
        ]);
    }

    /**
     * Desactive logiquement une valeur de LOV
     * @Route("/admin/lov/disable/{entity}/{id}", name="admin_lov_disable", methods={"GET","POST"})
     * @Security("is_granted('ROLE_SUPER_ADMIN')")
     */
    public function disable($entity, $id, TranslatorInterface $translator)
    {
        $em = $this->getDoctrine()->getManager();
        $valueOnList = $em->getRepository("App\Entity\LovManagement\\" . $entity)->findOneBy(['id' => $id]);

        $user = $this->getUser();
        $valueOnList->setUpdateUser($user);
        $valueOnList->setUpdateDate(new \Datetime());

        if ($valueOnList->getIsValid()) {
            $valueOnList->setIsValid(false);
            $this->addFlash('success', $translator->trans('lov.actions.flash.disable'));
        } else {
            $valueOnList->setIsValid(true);
            $this->addFlash('success', $translator->trans('lov.actions.flash.enable'));
        }

        try {
            $em->persist($valueOnList);
            $em->flush();
        } catch (\Doctrine\DBAL\DBALException $exception) {
            $this->addFlash('error', $exception->getMessage());
        }

        return $this->redirect($this->generateUrl('admin_lov_list', [
            'entity' => $entity,
        ]));
    }
}
