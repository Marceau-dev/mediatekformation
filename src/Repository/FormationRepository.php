<?php

namespace App\Repository;

use App\Entity\Formation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Repository permettant de gérer les requêtes liées aux formations.
 *
 * @extends ServiceEntityRepository<Formation>
 */
class FormationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Formation::class);
    }

    /**
    * Ajoute une formation en base de données.
    *
    * @param Formation $entity Formation à ajouter.
    * @return void
    */
    public function add(Formation $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }
    
    /**
    * Supprime une formation de la base de données.
    *
    * @param Formation $entity Formation à supprimer.
    * @return void
    */
    public function remove(Formation $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    /**
     * Retourne toutes les formations triées selon un champ et un ordre donnés.
     *
     * @param string $champ Champ utilisé pour le tri.
     * @param string $ordre Ordre du tri : ASC ou DESC.
     * @param string $table Nom de la relation utilisée si le champ appartient à une autre table.
     * @return Formation[]
     */
    public function findAllOrderBy($champ, $ordre, $table = ""): array
    {
        if ($table == "") {
            return $this->createQueryBuilder('f')
                            ->orderBy('f.' . $champ, $ordre)
                            ->getQuery()
                            ->getResult();
        } else {
            return $this->createQueryBuilder('f')
                            ->join('f.' . $table, 't')
                            ->orderBy('t.' . $champ, $ordre)
                            ->getQuery()
                            ->getResult();
        }
    }

    /**
     * Retourne les formations dont un champ contient une valeur donnée.
     * Si la valeur est vide, toutes les formations sont retournées.
     *
     * @param string $champ Champ utilisé pour la recherche.
     * @param string|null $valeur Valeur recherchée.
     * @param string $table Nom de la relation utilisée si le champ appartient à une autre table.
     * @return Formation[] Liste des formations correspondant à la recherche.
     */
    public function findByContainValue($champ, $valeur, $table = ""): array
    {
        $champsAutorises = ['title', 'name', 'id'];
        $tablesAutorisees = ['', 'playlist', 'categories'];

        if (!in_array($champ, $champsAutorises)) {
            $champ = 'title';
        }

        if (!in_array($table, $tablesAutorisees)) {
            $table = '';
        }
        
        if ($valeur == "") {
            return $this->findAll();
        }
        if ($table === "") {
            return $this->createQueryBuilder('f')
                            ->where('f.' . $champ . ' LIKE :valeur')
                            ->orderBy('f.publishedAt', 'DESC')
                            ->setParameter('valeur', '%' . $valeur . '%')
                            ->getQuery()
                            ->getResult();
        } else {
            return $this->createQueryBuilder('f')
                            ->join('f.' . $table, 't')
                            ->where('t.' . $champ . ' LIKE :valeur')
                            ->orderBy('f.publishedAt', 'DESC')
                            ->setParameter('valeur', '%' . $valeur . '%')
                            ->getQuery()
                            ->getResult();
        }
    }

    /**
     * Retourne les dernières formations publiées.
     *
     * @param int $nb Nombre de formations à retourner.
     * @return Formation[] Liste des formations les plus récentes.
     */
    public function findAllLasted($nb): array
    {
        return $this->createQueryBuilder('f')
                        ->orderBy('f.publishedAt', 'DESC')
                        ->setMaxResults($nb)
                        ->getQuery()
                        ->getResult();
    }

    /**
     * Retourne les formations associées à une playlist.
     *
     * @param int $idPlaylist Identifiant de la playlist.
     * @return Formation[] Liste des formations de la playlist.
     */
    public function findAllForOnePlaylist($idPlaylist): array
    {
        return $this->createQueryBuilder('f')
                        ->join('f.playlist', 'p')
                        ->where('p.id=:id')
                        ->setParameter('id', $idPlaylist)
                        ->orderBy('f.publishedAt', 'ASC')
                        ->getQuery()
                        ->getResult();
    }
}
