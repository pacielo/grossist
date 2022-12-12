<?php

namespace App\EventListener;

use App\Entity\UserManagement\Tracking;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Event\AuthenticationFailureEvent;
use Symfony\Component\Security\Core\AuthenticationEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\UserManagement\UserRepository;

/**
 * Listener responsible to change the redirection when a form is successfully filled
 */
class AuthentificationFailure implements EventSubscriberInterface
{
    private $router;
    private $dispatcher;
    private $entityManager;
    private $request;
    private $userRepository;

    public function __construct(UrlGeneratorInterface $router, EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager,RequestStack $requestStack,UserRepository $userRepository)
    {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
        $this->request = $requestStack->getCurrentRequest();
        $this->userRepository = $userRepository;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            AuthenticationEvents::AUTHENTICATION_FAILURE => 'onFailure',
        ];
    }

    public function onFailure($event)
    {
       
        if ($event instanceof AuthenticationFailureEvent) {} 
        $entity = new Tracking();
        $email = $event->getAuthenticationToken()->getCredentials()['email'];
        $user = $this->userRepository->findOneBy(['username'=>$email]);
        if ($user == null){
            $user = $this->userRepository->findOneBy(['email'=>$email]);
        }
        $now = new \DateTime('now');
        
        if($user){
            $entity->setUser($user);
            if($user->getLastLoginError() == null){
                $user->setLastLoginError($now);
                $user->setNbLoginError(1);
            }elseif($user->getLastLoginError()->modify("+1 hour") > $now ){
                $user->setNbLoginError($user->getNbLoginError()+1);
            }else{
                $user->setLastLoginError($now);
                $user->setNbLoginError(1);
            }
            $this->entityManager->persist($user);
            $this->entityManager->flush($user);
        }
        if($user && $user->getNbLoginError() && $user->getNbLoginError() >= 5){
            $entity->setAction('stopLogin');
        }else{
            $entity->setAction('errorLogin');
        }
        
        $entity->setQueryRequest(json_encode(['lastUsername' => $email, 'error' => '']));
        $entity->setPathInfo('/fr_FR/login');
        $entity->setHttpMethod($this->request->getMethod());
        $entity->setIpRequest($this->request->getClientIp());
        $entity->setLang($this->request->getLocale());
        $entity->setUriRequest($this->request->getUri());
        $entity->setCreated($now);
        
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);
    }

    
}
