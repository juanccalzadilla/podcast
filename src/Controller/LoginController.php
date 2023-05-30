<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'login')]
    public function index(AuthenticationUtils $authUtils): Response
    {
        
        //Si hay un usuario logueado, redirigimos a la página de inicio
        if ($this->getUser()) {
            return $this->redirectToRoute('podcast_index');
        }

        $error = $authUtils->getLastAuthenticationError();

        $lastUsername = $authUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);

    }
}
