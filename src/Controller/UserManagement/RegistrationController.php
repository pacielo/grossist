<?php

namespace App\Controller\UserManagement;

use App\Entity\UserManagement\User;
use App\Form\UserManagement\RegistrationType;
use App\Message\UserManagement\UserRegisterMessage;
use App\Service\CaptchaValidator;
use App\Utils\TokenGeneratorInterface;
use Doctrine\DBAL\DBALException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register(Request $request, UserPasswordEncoderInterface $passwordEncoder, TokenGeneratorInterface $generator, MessageBusInterface $messageBus, CaptchaValidator $captchaValidator, TranslatorInterface $translator, Security $security): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        if ($this->getUser()) {
            if ($security->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_users_index_new');
            } elseif ($security->isGranted('ROLE_GERANT')) {
                return $this->redirectToRoute('patient_index');
            } elseif ($security->isGranted('ROLE_AGRI')) {
                return $this->redirectToRoute('patient_index');
            }
        }

        $user = new User();
        $user->setUsername('abc');
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            if (!$captchaValidator->validateCaptcha($request->request->get('g-recaptcha-response'), $request->getClientIp())) {
                $form->addError(new FormError($translator->trans('message.captcha_wrong')));
            } else {
                try {
                    $user->setRoles(['ROLE_GERANT']);
                    $user->setIsEnable(false);
                    $user->setConfirmationToken($generator->generateToken());
                    $user->setUsername($user->getEmail());
                    $user->setPassword($passwordEncoder->encodePassword($user, $user->getPassword()));
                    $user->setLastChangePassword(new \DateTime('now'));
                    $user->setCreateUser($user);
                    $user->setUpdateUser($user);


                    $entityManager->persist($user);
                    $entityManager->flush();

                    $messageBus->dispatch(new UserRegisterMessage($user->getId()));

                    return $this->render('UserManagement/Registration/register_success.html.twig', [
                        'user' => $user,
                        'form' => $form->createView(),
                    ]);
                } catch (DBALException $exception) {
                    $this->addFlash('error', $exception->getMessage());
                } catch (Throwable $exception) {
                    $this->addFlash('error', $exception->getMessage());
                }
            }
        }

        return $this->render('UserManagement/Registration/register.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'captchakey' => $captchaValidator->getKey(),
        ]);
    }


    

    /**
     * @Route("/searchGerant", name="search_gerant", methods={"POST"})
     */
    public function searchPrespriteur(Request $request, TranslatorInterface $translator): Response
    {
        $em = $this->getDoctrine()->getManager();

        $data = json_decode($request->getContent(), true);

        if (isset($data['email'])) {
            $gerant = $em->getRepository("App\Entity\UserManagement\User")->findOneBy(['email' => $data['email']]);
            if ($gerant) { 
                return new JsonResponse([
                    'success' => 1,
                    'error' => 0,
                    'message' => 'OK',
                ]);
            }
        }

        return new JsonResponse([
            'success' => 0,
            'error' => 0,
            'message' => 'OK',
        ]);
    }
}
