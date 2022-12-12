<?php

namespace App\Service\UserManagement;

use App\Entity\UserManagement\User;
use App\Exception\AccountNotFoundException;
use App\Message\UserManagement\SendMailPharmacienApresActivationPDS;
use App\Repository\UserManagement\UserRepository;
use App\Service\AbstractService;
use App\Utils\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterService extends AbstractService
{
    private $em;    
    private $messageBus;
    private $repository;
    private $translator;

    public function __construct(ContainerInterface $container, EntityManagerInterface $entityManager, UserRepository $repository, MessageBusInterface $messageBus, TranslatorInterface $translator) 
    {
        parent::__construct($container);
        $this->em = $entityManager;
        $this->messageBus = $messageBus;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    public function confirmation(string $hash): void
    {
        $user = $this->repository->findOneBy([
            'confirmation_token' => $hash,
            'isValid'      => true,
            'isEnable'   => false,
        ]);

        if (!$user) {
            $this->addFlash('error', $this->translator->trans('message.token_not_found'));
            throw  new AccountNotFoundException();
        }

        $user->setIsEnable(true);
        $user->setconfirmationToken(null);

        $this->em->persist($user);
        $this->em->flush();
        
        
        $this->addFlash('success', $this->translator->trans('message.confirmation_enregistrement'));
    }

}
