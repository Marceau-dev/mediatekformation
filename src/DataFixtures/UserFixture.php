<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Classe de fixtures permettant de créer les utilisateurs de test.
 */
class UserFixture extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
    * Charge les utilisateurs de test dans la base de données.
    *
    * @param ObjectManager $manager Gestionnaire d'entités Doctrine.
    * @return void
    */
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'admin')
        );

        $manager->persist($user);
        
        $user2 = new User();
        $user2->setUsername('Marceau');
        $user2->setRoles(['ROLE_ADMIN']);
        $user2->setPassword(
            $this->passwordHasher->hashPassword($user2, 'Marceau-devadmin')
        );

        $manager->persist($user2);
        
        $manager->flush();
    }
}
