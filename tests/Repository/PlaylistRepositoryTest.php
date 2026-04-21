<?php

namespace App\Tests\Repository;

use App\Entity\Playlist;
use App\Repository\PlaylistRepository;

class PlaylistRepositoryTest extends RepositoryTestCase
{
    private PlaylistRepository $playlistRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->playlistRepository = $this->entityManager->getRepository(Playlist::class);
    }

    public function testAdd(): void
    {
        $playlist = new Playlist();
        $playlist->setName('Nouvelle playlist');
        $playlist->setDescription('Description test');

        $this->playlistRepository->add($playlist);

        $playlistTrouvee = $this->playlistRepository->findOneBy([
            'name' => 'Nouvelle playlist',
        ]);

        $this->assertNotNull($playlistTrouvee);
        $this->assertSame('Nouvelle playlist', $playlistTrouvee->getName());
    }

    public function testRemove(): void
    {
        $playlist = new Playlist();
        $playlist->setName('Playlist à supprimer');
        $playlist->setDescription('Description test');

        $this->playlistRepository->add($playlist);

        $this->playlistRepository->remove($playlist);

        $playlistSupprimee = $this->playlistRepository->findOneBy([
            'name' => 'Playlist à supprimer',
        ]);

        $this->assertNull($playlistSupprimee);
    }

    public function testFindAllOrderByNameAsc(): void
    {
        $playlists = $this->playlistRepository->findAllOrderBy('name', 'ASC');

        $this->assertSame('PHP', $playlists[0]->getName());
    }

    public function testFindAllOrderByNameDesc(): void
    {
        $playlists = $this->playlistRepository->findAllOrderBy('name', 'DESC');

        $this->assertSame('Symfony', $playlists[0]->getName());
    }

    public function testFindAllOrderByNbFormationsDesc(): void
    {
        $playlists = $this->playlistRepository->findAllOrderBy('nbFormations', 'DESC');

        $this->assertSame('Symfony', $playlists[0]->getName());
    }

    public function testFindByContainValueName(): void
    {
        $playlists = $this->playlistRepository->findByContainValue('name', 'Sym');

        $this->assertCount(1, $playlists);
        $this->assertSame('Symfony', $playlists[0]->getName());
    }

    public function testFindByContainValueCategorieId(): void
    {
        $playlists = $this->playlistRepository->findByContainValue(
            'id',
            $this->categorieWeb->getId(),
            'categories'
        );

        $this->assertCount(2, $playlists);
        $this->assertSame('PHP', $playlists[0]->getName());
    }

    public function testFindByContainValueEmptyReturnsAllOrderedByName(): void
    {
        $playlists = $this->playlistRepository->findByContainValue('name', '');

        $this->assertCount(2, $playlists);
        $this->assertSame('PHP', $playlists[0]->getName());
    }
}
