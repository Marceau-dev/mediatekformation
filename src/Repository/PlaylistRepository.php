<?php

namespace App\Repository;

use App\Entity\Playlist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository permettant de gérer les requêtes liées aux playlists.
 *
 * @extends ServiceEntityRepository<Playlist>
 */
class PlaylistRepository extends ServiceEntityRepository
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Playlist::class);
    }

    /**
    * Ajoute une playlist en base de données.
    *
    * @param Playlist $entity Playlist à ajouter.
    * @return void
    */
    public function add(Playlist $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    /**
    * Supprime une playlist de la base de données.
    *
    * @param Playlist $entity Playlist à supprimer.
    * @return void
    */
    public function remove(Playlist $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }
    
    /**
    * Retourne toutes les playlists triées par nom ou par nombre de formations.
    *
    * @param string $champ Champ de tri : 'name' ou 'nbFormations'
    * @param string $ordre Sens du tri : 'ASC' ou 'DESC'
    * @return Playlist[]
    */   
    public function findAllOrderBy($champ, $ordre): array
    {
        $champsAutorises = ['name', 'nbFormations'];
        $ordresAutorises = ['ASC', 'DESC'];

        if (!in_array($champ, $champsAutorises, true)) {
            $champ = 'name';
        }

        if (!in_array($ordre, $ordresAutorises, true)) {
            $ordre = 'ASC';
        }

        $query = $this->createQueryBuilder('p')
                ->leftJoin('p.formations', 'f')
                ->groupBy('p.id');

        if ($champ === 'nbFormations') {
            $query->orderBy('COUNT(f.id)', $ordre);
        } else {
            $query->orderBy('p.name', $ordre);
        }

        return $query->getQuery()->getResult();
    }



    /**
     * Enregistrements dont un champ contient une valeur
     * ou tous les enregistrements si la valeur est vide
     * @param type $champ
     * @param type $valeur
     * @param type $table si $champ dans une autre table
     * @return Playlist[]
     */
    public function findByContainValue($champ, $valeur, $table = ""): array
    {
        $tablesAutorisees = ['', 'categories'];
        $champsAutorises = [
            '' => ['name'],
            'categories' => ['id'],
        ];

        if (!in_array($table, $tablesAutorisees, true)) {
            $table = '';
        }

        if (!in_array($champ, $champsAutorises[$table], true)) {
            $champ = $table === 'categories' ? 'id' : 'name';
        }

        if ($valeur == "") {
            return $this->findAllOrderBy('name', 'ASC');
        }

        if ($table == "") {
            return $this->createQueryBuilder('p')
                            ->leftjoin('p.formations', 'f')
                            ->where('p.' . $champ . ' LIKE :valeur')
                            ->setParameter('valeur', '%' . $valeur . '%')
                            ->groupBy('p.id')
                            ->orderBy('p.name', 'ASC')
                            ->getQuery()
                            ->getResult();
        } else {
            return $this->createQueryBuilder('p')
                            ->leftjoin('p.formations', 'f')
                            ->leftjoin('f.categories', 'c')
                            ->where('c.' . $champ . ' LIKE :valeur')
                            ->setParameter('valeur', '%' . $valeur . '%')
                            ->groupBy('p.id')
                            ->orderBy('p.name', 'ASC')
                            ->getQuery()
                            ->getResult();
        }
    }
}
