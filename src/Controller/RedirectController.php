<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    #[Route('/', name: 'app_root')]
    public function redirectByRole(): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_dashboard_admin');
        }

        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_dashboard');
        }

        // Optionnel : en cas d'utilisateur sans rôle reconnu
        return $this->render('security/access_denied.html.twig');
    }
}
