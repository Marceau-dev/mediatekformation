<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

/**
 * Classe de fixtures permettant de charger des données de démonstration.
 */
class AppFixtures extends Fixture
{
    /**
    * Charge les données de démonstration dans la base de données.
    *
    * @param ObjectManager $manager Gestionnaire d'entités Doctrine.
    * @return void
    */
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }
}
