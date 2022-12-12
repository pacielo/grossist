<?php

namespace App\MessageHandler\UserManagement;

use App\Entity\User\UserManagement;
use App\Mailer\MailerInterface;
use App\Message\UserManagement\SendUserCredentialsMessage;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SendUserCredentialsHandler implements MessageHandlerInterface
{
    private $mailer;
    private $translator;
    private $router;
    private $twig;
    private $parameter;

    public function __construct(Environment $twig, MailerInterface $mailer, TranslatorInterface $translator, UrlGeneratorInterface $router, ParameterBagInterface $parameter) 
    {
        $this->mailer = $mailer;
        $this->translator = $translator;
        $this->router = $router;
        $this->twig = $twig;
        $this->parameter = $parameter;
    }

    public function __invoke(SendUserCredentialsMessage $message): void
    {
        $user = $message->getUser();
        $password = $message->getPassword();
        
        $this->mailer->sendMail($this->getSender(), array($user->getEmail()),  $this->translator->trans('mail.bienvenu'), $this->twig->render('UserManagement/Registration/emails/new_user_admin_welcome.html.twig', ['user' => $user, 'password' => $password])
        );

    }

    private function getSender(): array
    {
        return [$this->parameter->get('admin_email')];
    }
}
