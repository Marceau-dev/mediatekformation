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


#[Route('/admin/formations', name: 'admin.formations.')]
class AdminFormationsController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository, FormationRepository $formationRepository): Response
    {
        $formations = $formationRepository->findAll();

        return $this->render('admin/admin.formations.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'formations' => $formations,
        ]);
    }
    
    #[Route('/tri/{champ}/{ordre}/{table}', name: 'sort', defaults: ['table' => ''], methods: ['GET'])]
    public function sort(CategorieRepository $categorieRepository, FormationRepository $formationRepository, string $champ, string $ordre, string $table = ""): Response
    {
        $formations = $formationRepository->findAllOrderBy($champ, $ordre, $table);

        return $this->render('admin/admin.formations.html.twig', [
            'formations' => $formations,
            'categories' => $categorieRepository->findAll(),
        ]);
    }
    
    #[Route('/formation/{id}', name: 'showone', methods: ['GET'])]
    public function showOne(FormationRepository $formationRepository, int $id): Response
    {
        $formation = $formationRepository->find($id);

        return $this->render("pages/admin.formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
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
