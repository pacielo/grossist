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
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

/**
 * Listener responsible to change the redirection when a form is successfully filled
 */
class LoginListener implements EventSubscriberInterface
{
    private $router;
    private $dispatcher;
    private $entityManager;

    public function __construct(UrlGeneratorInterface $router, EventDispatcherInterface $dispatcher, EntityManagerInterface $entityManager)
    {
        $this->router = $router;
        $this->dispatcher = $dispatcher;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onLogin',
        ];
    }

    public function onLogin($event)
    {
        $request = $event->getRequest();
        $session = $request->getSession();
        $route = $request->get('_route');
        
        if ($event instanceof InteractiveLoginEvent) {
            $user = $event->getAuthenticationToken()->getUser();
        } 

        $entity = new Tracking();
        $entity->setAction('loginAction');
        $entity->setQueryRequest(json_encode(['lastUsername' => $user->getUsername(), 'error' => '']));
        $entity->setPathInfo('/fr_FR/login');
        $entity->setHttpMethod($request->getMethod());
        $entity->setIpRequest($request->getClientIp());
        $entity->setLang($request->getLocale());
        $entity->setUriRequest($request->getUri());
        $entity->setCreated((new \DateTime('now')));
        $entity->setUser($user);
        if($user){
            $user->setLastLoginError(null);
            $user->setNbLoginError(null);
            $this->entityManager->persist($user);
            $this->entityManager->flush($user);
        }
        $this->entityManager->persist($entity);
        $this->entityManager->flush($entity);

        if ($route != 'change_password' && $route != null) {
            $duration = 0;
            if ($user->getLastChangePassword()) {
                $now = new \DateTime();
                $interval = $user->getLastChangePassword()->diff($now);
                $duration = $interval->format('%a');
            } 
            if ($user->getChangePassword() || $user->getLastChangePassword() == null || $duration >= 90) {
                $this->dispatcher->addListener(KernelEvents::RESPONSE, [
                            $this,
                            'onKernelResponse'
                ]);
            }
        }
    }

    public function onKernelResponse(ResponseEvent $event)
    {
        $response = new RedirectResponse($this->router->generate('change_password'));
        $event->setResponse($response);
    }
}
