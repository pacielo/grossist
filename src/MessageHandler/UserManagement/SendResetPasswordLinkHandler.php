<?php

namespace App\MessageHandler\UserManagement;

use App\Entity\UserManagement\User;
use App\Mailer\MailerInterface;
use App\Message\UserManagement\SendResetPasswordLink;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class SendResetPasswordLinkHandler implements MessageHandlerInterface
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

    public function __invoke(SendResetPasswordLink $sendResetPasswordLink)
    {
        $user = $sendResetPasswordLink->getUser();

        $body = $this->getBody($user);

        $this->mailer->sendMail($this->getSender(), array($user->getEmail()), $this->getSubject(), $body);
    }

    private function getSender(): array
    {
        return [$this->parameter->get('admin_email')];
    }

    private function getSubject(): string
    {
        return $this->translator->trans('resetting.email.subject');
    }

    private function getConfirmationUrl(User $user): string
    {
        return $this->router->generate(
            'password_reset_confirm',
            ['token' => $user->getConfirmationToken()],
            0
        );
    }

    private function getBody(User $user): ?string
    {
        return $this->twig->render('UserManagement/Resetting/emails/reset.txt.twig', [
                  'confirmationUrl' => $this->getConfirmationUrl($user),
                  'user' => $user,
                ]);
    }
}
