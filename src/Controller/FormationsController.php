<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controleur des formations
 *
 * @author emds
 */
class FormationsController extends AbstractController
{

    /**
     *
     * Constante qui permet de ne pas répeter deux fois le meme chemin
    */
    private const VUE_FORMATIONS = 'pages/formations.html.twig';
    
    
    /**
     *
     * @var FormationRepository
     */
    private $formationRepository;
    
    /**
     *
     * @var CategorieRepository
     */
    private $categorieRepository;
    
    /**
    * Initialise le contrôleur avec les repositories nécessaires.
    *
    * @param FormationRepository $formationRepository Repository des formations.
    * @param CategorieRepository $categorieRepository Repository des catégories.
    */
    public function __construct(FormationRepository $formationRepository, CategorieRepository $categorieRepository)
    {
        $this->formationRepository = $formationRepository;
        $this->categorieRepository= $categorieRepository;
    }
    
    /**
    * Affiche la liste complète des formations.
    *
    * @return Response Page contenant la liste des formations.
    */
    #[Route('/formations', name: 'formations')]
    public function index(): Response
    {
        $formations = $this->formationRepository->findAll();
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VUE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
    * Affiche la liste des formations triées selon un champ et un ordre donnés.
    *
    * @param string $champ Champ utilisé pour le tri.
    * @param string $ordre Ordre du tri : ASC ou DESC.
    * @param string $table Relation utilisée si le champ appartient à une autre table.
    * @return Response Page contenant les formations triées.
    */
    #[Route('/formations/tri/{champ}/{ordre}/{table}', name:'formations.sort')]
    public function sort($champ, $ordre, $table=""): Response
    {
        $formations = $this->formationRepository->findAllOrderBy($champ, $ordre, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VUE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories
        ]);
    }

    /**
    * Affiche les formations correspondant à une recherche.
    *
    * @param string $champ Champ utilisé pour la recherche.
    * @param Request $request Requête HTTP contenant la valeur recherchée.
    * @param string $table Relation utilisée si le champ appartient à une autre table.
    * @return Response Page contenant les formations filtrées.
    */
    #[Route('/formations/recherche/{champ}/{table}', name: 'formations.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $formations = $this->formationRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VUE_FORMATIONS, [
            'formations' => $formations,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
    * Affiche le détail d'une formation.
    *
    * @param int $id Identifiant de la formation.
    * @return Response Page de détail de la formation.
    */
    #[Route('/formations/formation/{id}',name: 'formations.showone')]
    public function showOne($id): Response
    {
        $formation = $this->formationRepository->find($id);
        return $this->render("pages/formation.html.twig", [
            'formation' => $formation
        ]);
    }
    
}
