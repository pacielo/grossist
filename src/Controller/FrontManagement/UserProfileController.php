<?php

namespace App\Controller\FrontManagement;

use App\Entity\UserManagement\User;
use App\Form\UserManagement\UserType;
use App\Service\UserManagement\UserService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserProfileController extends AbstractController
{
    /**
     * user profile edit
     *
     * @Route("/profile", name="user_profile",methods={"GET", "POST"}))
     *
     * @Security("is_granted('ROLE_USER')")
     */
    public function profile(Request $request, UserService $service):Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->remove('email');

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $service->update($user);
        }

        return $this->render('UserFrontManagement/profile.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
