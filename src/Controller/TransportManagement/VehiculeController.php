<?php

namespace App\Controller\TransportManagement;

use App\Entity\TransportManagement\Vehicule;
use App\Entity\LovManagement\Ville;
use App\Form\TransportManagement\VehiculeType;
use App\Repository\LovManagement\TypeVoitureRepository;
use App\Repository\LovManagement\VilleRepository;
use App\Repository\LovManagement\CommuneRepository;
use App\Repository\TransportManagement\VehiculeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/transport/management/vehicule")
 */
class VehiculeController extends AbstractController
{
    /**
     * @Route("/", name="transport_management_vehicule_index", methods={"GET","POST"})
     */
    public function index(VehiculeRepository $vehiculeRepository, TypeVoitureRepository $typeVoitureRepository,VilleRepository $villeRepository, Request $request): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        foreach ($typeVoitureRepository->findAll() as $type) {
            $typeVehicule[$type->getTitle()] = $vehiculeRepository->findByType($type);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('transport_management_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transport_management/vehicule/index.html.twig', [
            'vehicules' => $vehiculeRepository->findAll(),
            'vehicule' => $vehicule,
            'typeVehicule' => $typeVehicule,
            'villes' => $villeRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ville/{id}", name="ville_vehicule", methods={"GET","POST"})
     */
    public function villeVehicule(VehiculeRepository $vehiculeRepository, CommuneRepository $communeRepository, TypeVoitureRepository $typeVoitureRepository,VilleRepository $villeRepository, Ville $ville, Request $request): Response
    {

        $typeVehicule = array();
        foreach ($vehiculeRepository->getByVille($ville->getId()) as $vehiculeVille) {
            foreach ($typeVoitureRepository->findAll() as $type) {
                if($vehiculeVille->getType()->getTitle() == $type->getTitle()){
                    $typeVehicule[$type->getTitle()] = $vehiculeVille;
                }
            }
        }

        return $this->render('transport_management/vehicule/vehicule_ville.html.twig', [
            'typeVehicule' => $typeVehicule,
            'ville' => $ville, 
            'commune' => $communeRepository->findByVille($ville),
            'villes' => $villeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="transport_management_vehicule_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $vehicule = new Vehicule();
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($vehicule);
            $entityManager->flush();

            return $this->redirectToRoute('transport_management_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transport_management/vehicule/new.html.twig', [
            'vehicule' => $vehicule,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transport_management_vehicule_show", methods={"GET"})
     */
    public function show(Vehicule $vehicule,VilleRepository $villeRepository): Response
    {
        return $this->render('transport_management/vehicule/show.html.twig', [
            'vehicule' => $vehicule,
            'villes' => $villeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="transport_management_vehicule_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,VilleRepository $villeRepository, Vehicule $vehicule): Response
    {
        $form = $this->createForm(VehiculeType::class, $vehicule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('transport_management_vehicule_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('transport_management/vehicule/edit.html.twig', [
            'vehicule' => $vehicule,
            'villes' => $villeRepository->findAll(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="transport_management_vehicule_delete", methods={"POST"})
     */
    public function delete(Request $request, Vehicule $vehicule): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vehicule->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($vehicule);
            $entityManager->flush();
        }

        return $this->redirectToRoute('transport_management_vehicule_index', [], Response::HTTP_SEE_OTHER);
    }
}
