<?php
namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\FormationRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of PlaylistsController
 *
 * @author emds
 */
class PlaylistsController extends AbstractController
{
    
    /**
     *
     * Constante qui permet de ne pas répeter deux fois le meme chemin
    */
    private const VUE_PLAYLISTS = 'pages/playlists.html.twig';
    
    
    /**
     *
     * @var PlaylistRepository
     */
    private $playlistRepository;
    
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
    * @param PlaylistRepository $playlistRepository Repository des playlists.
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @param FormationRepository $formationRepository Repository des formations.
    */
    public function __construct(
        PlaylistRepository $playlistRepository,
        CategorieRepository $categorieRepository,
        FormationRepository $formationRepository
    ) {
        $this->playlistRepository = $playlistRepository;
        $this->categorieRepository = $categorieRepository;
        $this->formationRepository = $formationRepository;
    }
    
    /**
     * Affiche la liste complète des playlists.
     *
     * @return Response Page contenant la liste des playlists.
     */
    #[Route('/playlists', name: 'playlists')]
    public function index(): Response
    {
        $playlists = $this->playlistRepository->findAllOrderBy('name','ASC');
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VUE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
    * Affiche la liste des playlists triées selon un champ et un ordre donnés.
    *
    * @param string $champ Champ utilisé pour le tri.
    * @param string $ordre Ordre du tri : ASC ou DESC.
    * @return Response Page contenant les playlists triées.
    */
    #[Route('/playlists/tri/{champ}/{ordre}', name: 'playlists.sort')]
    public function sort($champ, $ordre): Response
    {
        if ($champ === "name") {
            $playlists = $this->playlistRepository->findAllOrderBy($champ,$ordre);
        } else {
            $playlists = $this->playlistRepository->findAll();
        }
        
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VUE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories
        ]);
    }

    /**
    * Affiche les playlists correspondant à une recherche.
    *
    * @param string $champ Champ utilisé pour la recherche.
    * @param Request $request Requête HTTP contenant la valeur recherchée.
    * @param string $table Relation utilisée si le champ appartient à une autre table.
    * @return Response Page contenant les playlists filtrées.
    */
    #[Route('/playlists/recherche/{champ}/{table}', name: 'playlists.findallcontain')]
    public function findAllContain($champ, Request $request, $table=""): Response
    {
        $valeur = $request->get("recherche");
        $playlists = $this->playlistRepository->findByContainValue($champ, $valeur, $table);
        $categories = $this->categorieRepository->findAll();
        return $this->render(self::VUE_PLAYLISTS, [
            'playlists' => $playlists,
            'categories' => $categories,
            'valeur' => $valeur,
            'table' => $table
        ]);
    }

    /**
    * Affiche le détail d'une playlist avec ses formations et ses catégories.
    *
    * @param int $id Identifiant de la playlist.
    * @return Response Page de détail de la playlist.
    */
    #[Route('/playlists/playlist/{id}', name: 'playlists.showone')]
    public function showOne($id): Response
    {
        $playlist = $this->playlistRepository->find($id);
        $playlistCategories = $this->categorieRepository->findAllForOnePlaylist($id);
        $playlistFormations = $this->formationRepository->findAllForOnePlaylist($id);
        return $this->render("pages/playlist.html.twig", [
            'playlist' => $playlist,
            'playlistcategories' => $playlistCategories,
            'playlistformations' => $playlistFormations
        ]);
    }
    
}
