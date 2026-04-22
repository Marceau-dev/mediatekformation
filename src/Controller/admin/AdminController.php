<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Contrôleur principal du tableau de bord d'administration.
 *
 * @author marce
 */
#[Route('/admin', name: 'admin.')]
class AdminController extends AbstractController
{
    /**
    * Affiche la page d'accueil du back office.
    *
    * @return Response Page d'accueil de l'administration.
    */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/admin.index.html.twig');
    }
}
