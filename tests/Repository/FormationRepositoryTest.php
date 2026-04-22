<?php

namespace App\Tests\Repository;

use App\Entity\Formation;
use App\Repository\FormationRepository;

class FormationRepositoryTest extends RepositoryTestCase
{
    private FormationRepository $formationRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->formationRepository = $this->entityManager->getRepository(Formation::class);
    }

    public function testAdd(): void
    {
        $nouvelleFormation ='Nouvelle formation';
        $formation = $this->creerFormation(
            $nouvelleFormation,
            'video999',
            '2009-05-04',
            $this->playlistSymfony
        );

        $this->formationRepository->add($formation);

        $formationTrouvee = $this->formationRepository->findOneBy([
            'title' => $nouvelleFormation,
        ]);

        $this->assertNotNull($formationTrouvee);
        $this->assertSame($nouvelleFormation, $formationTrouvee->getTitle());
    }

    public function testRemove(): void
    {
        $formationPHP='Formation PHP';
        $formation = $this->formationRepository->findOneBy([
            'title' => $formationPHP,
        ]);

        $this->formationRepository->remove($formation);

        $formationSupprimee = $this->formationRepository->findOneBy([
            'title' => $formationPHP,
        ]);

        $this->assertNull($formationSupprimee);
    }

    public function testFindAllOrderByTitleAsc(): void
    {
        $formations = $this->formationRepository->findAllOrderBy('title', 'ASC');

        $this->assertSame('Formation Doctrine', $formations[0]->getTitle());
    }

    public function testFindAllOrderByPlaylistNameAsc(): void
    {
        $formations = $this->formationRepository->findAllOrderBy('name', 'ASC', 'playlist');

        $this->assertSame('Formation PHP', $formations[0]->getTitle());
    }

    public function testFindByContainValueTitle(): void
    {
        $formations = $this->formationRepository->findByContainValue('title', 'Symfony');

        $this->assertCount(1, $formations);
        $this->assertSame('Formation Symfony', $formations[0]->getTitle());
    }

    public function testFindByContainValuePlaylistName(): void
    {
        $formations = $this->formationRepository->findByContainValue('name', 'Symfony', 'playlist');

        $this->assertCount(2, $formations);
        $this->assertSame('Formation Doctrine', $formations[0]->getTitle());
    }

    public function testFindByContainValueCategorieId(): void
    {
        $formations = $this->formationRepository->findByContainValue(
            'id',
            $this->categorieWeb->getId(),
            'categories'
        );

        $this->assertCount(2, $formations);
        $this->assertSame('Formation Doctrine', $formations[0]->getTitle());
    }

    public function testFindAllLasted(): void
    {
        $formations = $this->formationRepository->findAllLasted(2);

        $this->assertCount(2, $formations);
        $this->assertSame('Formation Doctrine', $formations[0]->getTitle());
    }

    public function testFindAllForOnePlaylist(): void
    {
        $formations = $this->formationRepository->findAllForOnePlaylist($this->playlistSymfony->getId());

        $this->assertCount(2, $formations);
        $this->assertSame('Formation Symfony', $formations[0]->getTitle());
    }
}
