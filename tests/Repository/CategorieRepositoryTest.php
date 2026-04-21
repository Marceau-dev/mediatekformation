<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Repository\CategorieRepository;

class CategorieRepositoryTest extends RepositoryTestCase
{
    private CategorieRepository $categorieRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->categorieRepository = $this->entityManager->getRepository(Categorie::class);
    }

    public function testAdd(): void
    {
        $categorie = new Categorie();
        $categorie->setName('Nouvelle catégorie');

        $this->categorieRepository->add($categorie);

        $categorieTrouvee = $this->categorieRepository->findOneBy([
            'name' => 'Nouvelle catégorie',
        ]);

        $this->assertNotNull($categorieTrouvee);
        $this->assertSame('Nouvelle catégorie', $categorieTrouvee->getName());
    }

    public function testRemove(): void
    {
        $categorie = new Categorie();
        $categorie->setName('Catégorie à supprimer');

        $this->categorieRepository->add($categorie);

        $this->categorieRepository->remove($categorie);

        $categorieSupprimee = $this->categorieRepository->findOneBy([
            'name' => 'Catégorie à supprimer',
        ]);

        $this->assertNull($categorieSupprimee);
    }

    public function testFindAllForOnePlaylist(): void
    {
        $categories = $this->categorieRepository->findAllForOnePlaylist($this->playlistSymfony->getId());

        $this->assertCount(2, $categories);
        $this->assertSame('Framework', $categories[0]->getName());
        $this->assertSame('Web', $categories[1]->getName());
    }
}
