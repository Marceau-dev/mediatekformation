<?php

namespace App\Tests\Repository;

use App\Entity\Categorie;
use App\Entity\Formation;
use App\Entity\Playlist;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class RepositoryTestCase extends  WebTestCase
{
    protected EntityManagerInterface $entityManager;

    protected Playlist $playlistSymfony;
    protected Playlist $playlistPhp;
    protected Categorie $categorieWeb;
    protected Categorie $categorieFramework;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        $this->viderBase();
        $this->chargerDonnees();
    }

    protected function viderBase(): void
    {
        $this->entityManager->createQuery('DELETE FROM App\Entity\Formation f')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Categorie c')->execute();
        $this->entityManager->createQuery('DELETE FROM App\Entity\Playlist p')->execute();
    }

    protected function chargerDonnees(): void
    {
        $this->playlistSymfony = new Playlist();
        $this->playlistSymfony->setName('Symfony');
        $this->playlistSymfony->setDescription('Playlist Symfony');

        $this->playlistPhp = new Playlist();
        $this->playlistPhp->setName('PHP');
        $this->playlistPhp->setDescription('Playlist PHP');

        $this->categorieWeb = new Categorie();
        $this->categorieWeb->setName('Web');

        $this->categorieFramework = new Categorie();
        $this->categorieFramework->setName('Framework');

        $formationSymfony = $this->creerFormation(
            'Formation Symfony',
            'video001',
            '2022-01-01',
            $this->playlistSymfony,
            [$this->categorieFramework]
        );

        $formationDoctrine = $this->creerFormation(
            'Formation Doctrine',
            'video002',
            '2024-01-01',
            $this->playlistSymfony,
            [$this->categorieWeb]
        );

        $formationPhp = $this->creerFormation(
            'Formation PHP',
            'video003',
            '2023-01-01',
            $this->playlistPhp,
            [$this->categorieWeb]
        );

        $this->entityManager->persist($this->playlistSymfony);
        $this->entityManager->persist($this->playlistPhp);
        $this->entityManager->persist($this->categorieWeb);
        $this->entityManager->persist($this->categorieFramework);
        $this->entityManager->persist($formationSymfony);
        $this->entityManager->persist($formationDoctrine);
        $this->entityManager->persist($formationPhp);

        $this->entityManager->flush();
    }

    protected function creerFormation(
        string $title,
        string $videoId,
        string $publishedAt,
        Playlist $playlist,
        array $categories = []
    ): Formation {
        $formation = new Formation();
        $formation->setTitle($title);
        $formation->setDescription('Description ' . $title);
        $formation->setVideoId($videoId);
        $formation->setPublishedAt(new \DateTime($publishedAt));
        $formation->setPlaylist($playlist);

        foreach ($categories as $categorie) {
            $formation->addCategory($categorie);
        }

        return $formation;
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->entityManager->close();
    }
}
