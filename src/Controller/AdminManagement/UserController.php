<?php

namespace App\Controller\AdminManagement;

use App\Entity\UserManagement\User;
use App\Form\UserManagement\OtherUserShowType;
use App\Form\UserManagement\OtherUserType;
use App\Message\UserManagement\SendUserCredentialsMessage;
use App\Repository\UserManagement\UserRepository;
use App\Service\UserManagement\UserService;
use App\Utils\TokenGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/admin/users/index_other_roles", name="admin_users_index_other_roles", methods={"GET", "POST"})
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function indexUser(UserRepository $userRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $generator, TranslatorInterface $translator, MessageBusInterface $messageBus): Response
    {

        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setUsername('abc');
        $form = $this->createForm(OtherUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setUsername($user->getEmail());
                $user->setIsEnable(true);
                $user->setRoles($form['listOfRoles']->getData());
                //$user->setConfirmationToken($generator->generateToken());
                $password = $user->getPassword();
                $user->setPassword($passwordEncoder->encodePassword($user, $password));

                $user->setCreateUser($this->getUser());
                $user->setUpdateUser($this->getUser());
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', $translator->trans('user.action.added'));

                $messageBus->dispatch(new SendUserCredentialsMessage($user, $password));

                return $this->redirectToRoute('admin_users_index_other_roles');
            } catch (DBALException $exception) {
                $this->addFlash('error', $exception->getMessage());
            } catch (Throwable $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }


        return $this->render('UserManagement/User/index.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'action' => 'otherRoles',
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/admin/user/new", name="admin_user_new")
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function newUser(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $generator, TranslatorInterface $translator, MessageBusInterface $messageBus): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = new User();
        $user->setUsername('abc');
        $form = $this->createForm(OtherUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $user->setUsername($user->getEmail());
                $user->setIsEnable(true);
                $user->setRoles($form['listOfRoles']->getData());
                //$user->setConfirmationToken($generator->generateToken());
                $password = $user->getPassword();
                $user->setPassword($passwordEncoder->encodePassword($user, $password));

                $user->setCreateUser($this->getUser());
                $user->setUpdateUser($this->getUser());
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', $translator->trans('user.action.added'));

                $messageBus->dispatch(new SendUserCredentialsMessage($user, $password));

                return $this->redirectToRoute('admin_users_index_other_roles');
            } catch (DBALException $exception) {
                $this->addFlash('error', $exception->getMessage());
            } catch (Throwable $exception) {
                $this->addFlash('error', $exception->getMessage());
            }
        }

        return $this->render('UserManagement/User/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/admin/user/show/{id}", name="admin_user_show", methods={"GET"})
     */
    public function showUser(Request $request, User $user): Response
    {
        $form = $this->createForm(OtherUserShowType::class, $user);

        return $this->render('UserManagement/User/show.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'delete' => $request->get('delete'),
        ]);
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/admin/user/edit/{id}",methods={"GET", "POST"}, name="admin_user_edit")
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function editUser(Request $request, User $user, UserService $service): Response
    {
        $form = $this->createForm(OtherUserType::class, $user);
        $form->remove('password');
        $form->remove('hasAcceptedCGU');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUsername($user->getEmail());
            $user->setRoles($form['listOfRoles']->getData());
            $service->update($user);

            return $this->redirectToRoute('admin_users_index_other_roles');
        }

        return $this->render('UserManagement/User/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    /**
     * activer ou desactiver un user (tout rôle confondu).
     *
     * @Route("/active/{id}", name="admin_user_active", methods={"GET", "POST"})
     *
     * @Security("is_granted('ROLE_ADMIN')")
     */
    public function active(User $user, UserService $service): Response
    {
        if (isset($user) && $user->getIsValid()) {
            $user->setIsValid(false);
            $service->update($user);
        } elseif (isset($user) && !$user->getIsValid()) {
            $user->setIsValid(true);
            $service->update($user);
        }

        if (\in_array('ROLE_GERANT', $user->getRoles(), true)) {
            return $this->redirect($this->generateUrl('admin_users_index_prescripteur'));
        } elseif (\in_array('ROLE_ADMIN', $user->getRoles(), true) || \in_array('ROLE_AGRI', $user->getRoles(), true)) {
            return $this->redirect($this->generateUrl('admin_users_index_other_roles'));
        }
    }

    /*
    * Deletes an User entity.
    *
    * @Route("/admin/user/delete/{id}", methods={"DELETE","GET","POST"}, name="admin_user_delete")
    *
    * @Security("is_granted('ROLE_ADMIN')")
    */
    // public function delete(Request $request, User $user, UserService $service): Response
    // {
    //     if (!$this->isCsrfTokenValid('delete', $request->request->get('token'))) {
    //         return $this->redirectToRoute('admin_users_index');
    //     }

    //     $service->remove($user);
    //     return $this->redirectToRoute('admin_users_index');
    // }

}
