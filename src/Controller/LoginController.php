<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur permettant de gérer l'authentification des utilisateurs.
 */
class LoginController extends AbstractController
{
    /**
    * Affiche le formulaire de connexion et gère les erreurs d'authentification.
    *
    * @param AuthenticationUtils $authenticationUtils Service fournissant les informations d'authentification.
    * @return Response Page de connexion.
    */
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
    * Déconnecte l'utilisateur connecté.
    *
    * @return void
    */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('Cette méthode est interceptée par Symfony.');
    }
}
