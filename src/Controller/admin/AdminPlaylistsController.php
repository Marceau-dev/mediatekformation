<?php

namespace App\Controller\admin;

use App\Entity\Playlist;
use App\Form\PlaylistType;
use App\Repository\CategorieRepository;
use App\Repository\PlaylistRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Contrôleur d'administration permettant de gérer les playlists.
 *
 * @author marce
 */
#[Route('/admin/playlists', name: 'admin.playlists.')]
class AdminPlaylistsController extends AbstractController
{
    /**
    * Affiche la liste des playlists dans le back office.
    *
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @param PlaylistRepository $playlistRepository Repository des playlists.
    * @return Response Page d'administration des playlists.
    */
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CategorieRepository $categorieRepository, PlaylistRepository $playlistRepository): Response
    {
        $categories = $categorieRepository->findAll();
        $playlists = $playlistRepository->findAllOrderBy('name', 'ASC');

        return $this->render('admin/admin.playlists.html.twig', [
            'playlists' => $playlists,
            'categories' => $categories,

        ]);
    }
    
    /**
    * Affiche les playlists triées dans le back office.
    *
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @param PlaylistRepository $playlistRepository Repository des playlists.
    * @param string $champ Champ utilisé pour le tri.
    * @param string $ordre Ordre du tri : ASC ou DESC.
    * @return Response Page d'administration contenant les playlists triées.
    */
    #[Route('/tri/{champ}/{ordre}', name: 'sort', methods: ['GET'])]
    public function sort(
        CategorieRepository $categorieRepository,
        PlaylistRepository $playlistRepository,
        string $champ,
        string $ordre
    ): Response {
        $playlists = $playlistRepository->findAllOrderBy($champ, $ordre);

        return $this->render('admin/admin.playlists.html.twig', [
            'categories' => $categorieRepository->findAll(),
            'playlists' => $playlists,
        ]);
    }

    /**
    * Modifie une playlist existante à partir du formulaire d'administration.
    *
    * @param int $id Identifiant de la playlist à modifier.
    * @param Request $request Requête HTTP contenant les données du formulaire.
    * @param PlaylistRepository $playlistRepository Repository des playlists.
    * @return Response Page du formulaire ou redirection après validation.
    */
    #[Route('/edit/{id}', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(
        int $id,
        Request $request,
        PlaylistRepository $playlistRepository
    ): Response {
        $playlist = $playlistRepository->find($id);

        if (!$playlist) {
            return $this->redirectToRoute('admin.playlists.index');
        }

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlistRepository->add($playlist);

            return $this->redirectToRoute('admin.playlists.index');
        }

        return $this->render('admin/admin.playlist.edit.html.twig', [
            'formPlaylist' => $form->createView(),
            'playlist' => $playlist,
        ]);
    }

    /**
    * Supprime une playlist après vérification du jeton CSRF.
    * La suppression est refusée si la playlist contient des formations.
    *
    * @param int $id Identifiant de la playlist à supprimer.
    * @param Request $request Requête HTTP contenant le jeton CSRF.
    * @param PlaylistRepository $playlistRepository Repository des playlists.
    * @return Response Redirection vers la liste des playlists.
    */
    #[Route('/suppr/{id}', name: 'suppr', methods: ['POST'])]
    public function suppr(
        int $id,
        Request $request,
        PlaylistRepository $playlistRepository
    ): Response {
        $playlist = $playlistRepository->find($id);

        if (!$playlist) {
            return $this->redirectToRoute('admin.playlists.index');
        }

        if (!$this->isCsrfTokenValid(
            'suppr_playlist_' . $playlist->getId(),
            $request->request->get('_token')
        )) {
            return $this->redirectToRoute('admin.playlists.index');
        }

        if ($playlist->getNombreFormations() > 0) {
            $this->addFlash(
                'danger',
                'Impossible de supprimer une playlist contenant des formations.'
            );

            return $this->redirectToRoute('admin.playlists.index');
        }

        $playlistRepository->remove($playlist);

        return $this->redirectToRoute('admin.playlists.index');
    }
    
    /**
    * Ajoute une nouvelle playlist à partir du formulaire d'administration.
    *
    * @param Request $request Requête HTTP contenant les données du formulaire.
    * @param PlaylistRepository $playlistRepository Repository des playlists.
    * @return Response Page du formulaire ou redirection après validation.
    */
    #[Route('/ajout', name: 'ajout', methods: ['GET', 'POST'])]
    public function ajout(Request $request, PlaylistRepository $playlistRepository): Response
    {
        $playlist = new Playlist();

        $form = $this->createForm(PlaylistType::class, $playlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $playlistRepository->add($playlist);

            return $this->redirectToRoute('admin.playlists.index');
        }

        return $this->render('admin/admin.playlist.ajout.html.twig', [
            'formPlaylist' => $form->createView(),
            'playlist' => $playlist,
        ]);
    }

    /**
    * Affiche les playlists filtrées dans le back office.
    *
    * @param string $champ Champ utilisé pour la recherche.
    * @param Request $request Requête HTTP contenant la valeur recherchée.
    * @param CategorieRepository $categorieRepository Repository des catégories.
    * @param PlaylistRepository $playlistRepository Repository des playlists.
    * @param string $table Relation utilisée si le champ appartient à une autre table.
    * @return Response Page d'administration contenant les playlists filtrées.
    */
    #[Route('/recherche/{champ}/{table}', name: 'findallcontain', defaults: ['table' => ''], methods: ['POST'])]
    public function findAllContain(
        string $champ,
        Request $request,
        CategorieRepository $categorieRepository,
        PlaylistRepository $playlistRepository,
        string $table = ''
    ): Response {
        $valeur = $request->request->get('recherche');

        $categories = $categorieRepository->findAll();
        $playlists = $playlistRepository->findByContainValue($champ, $valeur, $table);

        return $this->render('admin/admin.playlists.html.twig', [
            'categories' => $categories,
            'playlists' => $playlists,
            'valeur' => $valeur,
            'table' => $table,
        ]);
    }
}
