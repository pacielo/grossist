<?php

namespace App\Controller\UserManagement;

use App\Exception\AccountNotFoundException;
use App\Service\UserManagement\RegisterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ConfirmationController extends AbstractController
{
    /**
     * @Route("/register-confirmation/{hash}", name="register-confirmation")
     */
    public function confirmation(Request $request, string $hash, RegisterService $service): RedirectResponse
    {
        try {
            $service->confirmation($hash);
        } catch (AccountNotFoundException $e) {
            return $this->redirectToRoute('password_reset');
        }

        return $this->redirectToRoute('app_login');
    }
}
