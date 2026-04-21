<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

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
 * Description of AdminPlaylistsController
 *
 * @author marce
 */
#[Route('/admin/playlists', name: 'admin.playlists.')]
class AdminPlaylistsController extends AbstractController
{
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
