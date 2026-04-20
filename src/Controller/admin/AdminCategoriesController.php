<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
/**
 * Description of AdminCategoriesController
 *
 * @author marce
 */
#[Route('/admin/categories', name: 'admin.categories.')]
class AdminCategoriesController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('admin/section.html.twig', [
            'sectionTitle' => 'Catégories',
            'sectionDescription' => "La gestion des catégories sera ajoutée à  l'étape suivante.",
        ]);
    }
}
