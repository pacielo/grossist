<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as RouteSecurity;

final class DefaultController extends AbstractController
{
    /**
     * Fonction index.
     *
     * @Route("/", name="home", methods="GET")
     */
    public function index(Request $request, Security $security)
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_users_index_new');
        } elseif ($security->isGranted('ROLE_GERANT')) {
            return $this->redirectToRoute('home_patient');            
        } elseif ($security->isGranted('ROLE_AGRI')) {
            return $this->redirectToRoute('home_patient');  
        } 
        // elseif ($security->isGranted('ROLE_USER')) {
        //     return $this->redirectToRoute('user_profile');                        
        // }

        return $this->redirectToRoute('app_login');
    }
	


}
