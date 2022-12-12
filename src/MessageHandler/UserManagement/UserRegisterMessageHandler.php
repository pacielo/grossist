<?php

namespace App\MessageHandler\UserManagement;

use App\Entity\User\UserManagement;
use App\Mailer\MailerInterface;
use App\Message\UserManagement\UserRegisterMessage;
use App\Repository\UserManagement\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class UserRegisterMessageHandler implements MessageHandlerInterface
{
    private $mailer;
    private $translator;
    private $router;
    private $twig;
    private $parameter;
    private $userRepository;

    public function __construct(Environment $twig, MailerInterface $mailer, TranslatorInterface $translator, UrlGeneratorInterface $router, ParameterBagInterface $parameter, UserRepository $userRepository) 
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->router = $router;
        $this->twig = $twig;
        $this->parameter = $parameter;
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserRegisterMessage $message): void
    {
        $user = $this->userRepository->find($message->getUserId());
        
        $this->mailer->sendMail($this->getSender(), array($user->getEmail()),  $this->translator->trans('mail.bienvenu'), $this->twig->render('UserManagement/Registration/emails/new_user_welcome.html.twig', ['user' => $user])
        );

    }

    private function getSender(): array
    {
        return [$this->parameter->get('admin_email')];
    }
}
