<?php

namespace App\Controller\FrontManagement;

use App\Form\UserManagement\ResetPasswordType;
use App\Repository\UserManagement\ResettingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ChangePasswordController extends AbstractController
{
    /**
     * @Route("/change_password", methods={"GET|POST"}, name="change_password")
     */
    public function changePassword(ResettingRepository $repository, Request $request, TranslatorInterface $translator): Response
    {
        if (!$this->getUser()) {
            return new RedirectResponse($this->generateUrl('app_login'));
        }

        $user = $this->getUser();
        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $repository->setPassword($user, $user->getPassword());
            $this->addFlash('success', $translator->trans('message.password_has_been_reset'));

            return $this->redirectToRoute('home');            
        }

        return $this->render('UserFrontManagement/password_change.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }
}
