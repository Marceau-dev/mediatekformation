<?php

namespace App\Controller\admin;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur d'administration permettant de gérer les catégories.
 *
 * @author marce
 */
#[Route('/admin/categories', name: 'admin.categories.')]
class AdminCategoriesController extends AbstractController
{
    /**
    * Affiche la liste des catégories dans le back office.
    *
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @return Response Page d'administration des catégories.
    */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository): Response
    {
        return $this->render('admin/admin.categories.html.twig', [
            'categories' => $categorieRepository->findBy([], ['name' => 'ASC']),
        ]);
    }

    /**
    * Ajoute une nouvelle catégorie après vérification du jeton CSRF.
    *
    * @param Request $request Requête HTTP contenant le nom de la catégorie et le jeton CSRF.
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @return Response Redirection vers la liste des catégories.
    */
    #[Route('/ajout', name: 'ajout', methods: ['POST'])]
    public function ajout(
        Request $request,
        CategorieRepository $categorieRepository
    ): Response {
        if (!$this->isCsrfTokenValid('ajout_categorie', $request->request->get('_token'))) {
            return $this->redirectToRoute('admin.categories.index');
        }

        $name = trim((string) $request->request->get('name'));

        if ($name === '') {
            $this->addFlash('danger', 'Le nom de la catégorie est obligatoire.');

            return $this->redirectToRoute('admin.categories.index');
        }

        $categorieExistante = $categorieRepository->findOneBy([
            'name' => $name,
        ]);

        if ($categorieExistante) {
            $this->addFlash('danger', 'Cette catégorie existe déjà.');

            return $this->redirectToRoute('admin.categories.index');
        }

        $categorie = new Categorie();
        $categorie->setName($name);

        $categorieRepository->add($categorie);

        $this->addFlash('success', 'La catégorie a été ajoutée.');

        return $this->redirectToRoute('admin.categories.index');
    }

    /**
    * Supprime une catégorie après vérification du jeton CSRF.
    * La suppression est refusée si la catégorie est rattachée à une formation.
    *
    * @param int $id Identifiant de la catégorie à supprimer.
    * @param Request $request Requête HTTP contenant le jeton CSRF.
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @return Response Redirection vers la liste des catégories.
    */
    #[Route('/suppr/{id}', name: 'suppr', methods: ['POST'])]
    public function suppr(
        int $id,
        Request $request,
        CategorieRepository $categorieRepository
    ): Response {
        $categorie = $categorieRepository->find($id);

        if (!$categorie) {
            return $this->redirectToRoute('admin.categories.index');
        }

        if (!$this->isCsrfTokenValid(
            'suppr_categorie_' . $categorie->getId(),
            $request->request->get('_token')
        )) {
            return $this->redirectToRoute('admin.categories.index');
        }

        if ($categorie->getFormations()->count() > 0) {
            $this->addFlash(
                'danger',
                'Impossible de supprimer une catégorie rattachée à une formation.'
            );

            return $this->redirectToRoute('admin.categories.index');
        }

        $categorieRepository->remove($categorie);

        $this->addFlash('success', 'La catégorie a été supprimée.');

        return $this->redirectToRoute('admin.categories.index');
    }
}
