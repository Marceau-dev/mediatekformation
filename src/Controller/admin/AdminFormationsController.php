<?php

namespace App\Controller\admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\FormationRepository;
use App\Entity\Formation;
use App\Form\FormationType;
use App\Repository\CategorieRepository;


/**
 * Contrôleur d'administration permettant de gérer les formations.
 */
#[Route('/admin/formations', name: 'admin.formations.')]
class AdminFormationsController extends AbstractController
{
    /**
    * Affiche la liste des formations dans le back office.
    *
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @param FormationRepository $formationRepository Repository des formations.
    * @return Response Page d'administration des formations.
    */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository, FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();

        return $this->render('admin/admin.formations.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'formations' => $formations,
        ]);
    }
    
    /**
    * Affiche les formations triées dans le back office.
    *
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @param FormationRepository $formationRepository Repository des formations.
    * @param string $champ Champ utilisé pour le tri.
    * @param string $ordre Ordre du tri : ASC ou DESC.
    * @param string $table Relation utilisée si le champ appartient à une autre table.
    * @return Response Page d'administration contenant les formations triées.
    */
    #[Route('/tri/{champ}/{ordre}/{table}', name: 'sort', defaults: ['table' => ''], methods: ['GET'])]
    public function sort(CategorieRepository $categorieRepository, FormationRepository $formationRepository, string $champ, string $ordre, string $table = ""): Response
    {
        $formations = $formationRepository->findAllOrderBy($champ, $ordre, $table);

        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'categories' => $categorieRepository->findAll(),
        ]);
    }
    
    /**
    * Affiche le détail d'une formation depuis le back office.
    *
    * @param FormationRepository $formationRepository Repository des formations.
    * @param int $id Identifiant de la formation.
    * @return Response Page de détail de la formation.
    */
    #[Route('/formation/{id}', name: 'showone', methods: ['GET'])]
    public function showOne(FormationRepository $formationRepository, int $id): Response
    {
        $formation = $formationRepository->find($id);

        return $this->render("pages/admin.formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
    /**
    * Supprime une formation après vérification du jeton CSRF.
    *
    * @param int $id Identifiant de la formation à supprimer.
    * @param Request $request Requête HTTP contenant le jeton CSRF.
    * @param FormationRepository $formationRepository Repository des formations.
    * @return Response Redirection vers la liste des formations.
    */
    #[Route('/suppr/{id}', name: 'suppr', methods: ['POST'])]
    public function suppr(
        int $id,
        Request $request,
        FormationRepository $formationRepository
    ): Response {
        $formation = $formationRepository->find($id);

        if (!$formation) {
            return $this->redirectToRoute('admin.formations.index');
        }

        if (!$this->isCsrfTokenValid('suppr_formation_' . $formation->getId(), $request->request->get('_token'))) {
            return $this->redirectToRoute('admin.formations.index');
        }

        $formationRepository->remove($formation);

        return $this->redirectToRoute('admin.formations.index');
    }

    
    /**
    * Modifie une formation existante à partir du formulaire d'administration.
    *
    * @param int $id Identifiant de la formation à modifier.
    * @param Request $request Requête HTTP contenant les données du formulaire.
    * @param FormationRepository $formationRepository Repository des formations.
    * @return Response Page du formulaire ou redirection après validation.
    */
    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        FormationRepository $formationRepository
    ): Response {
        $formation = $formationRepository->find($id);

        if (!$formation) {
            return $this->redirectToRoute('admin.formations.index');
        }

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->add($formation);

            return $this->redirectToRoute('admin.formations.index');
        }

        return $this->render('admin/admin.formation.edit.html.twig', [
            'formFormation' => $form->createView(),
            'formation' => $formation,
        ]);
    }

    /**
    * Ajoute une nouvelle formation à partir du formulaire d'administration.
    *
    * @param Request $request Requête HTTP contenant les données du formulaire.
    * @param FormationRepository $formationRepository Repository des formations.
    * @return Response Page du formulaire ou redirection après validation.
    */
    #[Route('/ajout', name: 'ajout', methods: ['GET', 'POST'])]
    public function ajout(Request $request, FormationRepository $formationRepository): Response
    {
        $formation = new Formation();

        $form = $this->createForm(FormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formationRepository->add($formation);

            return $this->redirectToRoute('admin.formations.index');
        }

        return $this->render('admin/admin.formation.ajout.html.twig', [
            'formFormation' => $form->createView(),
            'formation' => $formation,
        ]);
    }
    
    /**
    * Affiche les formations filtrées dans le back office.
    *
    * @param string $champ Champ utilisé pour la recherche.
    * @param Request $request Requête HTTP contenant la valeur recherchée.
    * @param FormationRepository $formationRepository Repository des formations.
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @param string $table Relation utilisée si le champ appartient à une autre table.
    * @return Response Page d'administration contenant les formations filtrées.
    */
    #[Route('/recherche/{champ}/{table}', name: 'findallcontain', defaults: ['table' => ''], methods: ['POST'])]
    public function findAllContain(
        string $champ,
        Request $request,
        FormationRepository $formationRepository,
        CategorieRepository $categorieRepository,
        string $table = ""
    ): Response {
        $valeur = $request->request->get('recherche');

        $formations = $formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $categorieRepository->findAll();

        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }
}
