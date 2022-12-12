<?php
namespace App\Service\UserManagement;

use App\Entity\UserManagement\User;
use App\Message\UserManagement\SendUserCreated;
use App\Service\AbstractService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserService extends AbstractService
{
    private $em;
    private $translator;

    public function __construct(EntityManagerInterface $entityManager, ContainerInterface $container, TranslatorInterface $translator) 
    {
        parent::__construct($container);
        $this->em = $entityManager;
        $this->translator = $translator;
    }

    public function update(User $user): bool
    {
        return $this->save($user, 'update');
    }

    private function setRoles(User $user, string $role): User
    {
        if ($role == 'ROLE_ADMIN') {
            $user->setRoles(['ROLE_ADMIN', 'ROLE_USER']);
        } else {
            $user->setRoles(['ROLE_USER']);
        }

        return $user;
    }

    private function save(User $user, string $action): bool
    {
        try {
            $this->em->persist($user);
            $this->em->flush();
            switch ($action) {
                case 'update':
                    $this->addFlash('success', $this->translator->trans('message.updated'));
                    break;
            }

            return true;
        } catch (\Doctrine\DBAL\DBALException $exception) {
            $this->addFlash('danger', $exception->getMessage());
        } catch (\Exception $exception) {
            $this->addFlash('danger', $exception->getMessage());
        }

        return false;
    }
}
