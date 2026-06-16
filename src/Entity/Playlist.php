<?php

namespace App\Entity;

use App\Repository\PlaylistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Entité représentant une playlist regroupant plusieurs formations.
 */
#[ORM\Entity(repositoryClass: PlaylistRepository::class)]
class Playlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    /**
     * @var Collection<int, Formation>
     */
    #[ORM\OneToMany(targetEntity: Formation::class, mappedBy: 'playlist')]
    private Collection $formations;

    #[ORM\Column]
    private ?int $like_play = 0;

    #[ORM\Column]
    private ?int $unlike_play = 0;

    public function __construct()
    {
        $this->formations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Formation>
     */
    public function getFormations(): Collection
    {
        return $this->formations;
    }
    
    /**
    * Retourne le nombre de formations associées à la playlist.
    *
    * @return int Nombre de formations.
    */
    public function getNombreFormations(): int
    {
        return $this->formations->count();
    }

    /**
    * Ajoute une formation à la playlist.
    *
    * @param Formation $formation Formation à ajouter.
    * @return static
    */
    public function addFormation(Formation $formation): static
    {
        if (!$this->formations->contains($formation)) {
            $this->formations->add($formation);
            $formation->setPlaylist($this);
        }

        return $this;
    }

    /**
    * Retire une formation de la playlist.
    *
    * @param Formation $formation Formation à retirer.
    * @return static
    */
    public function removeFormation(Formation $formation): static
    {
        if ($this->formations->removeElement($formation)) {
            // set the owning side to null (unless already changed)
            if ($formation->getPlaylist() === $this) {
                $formation->setPlaylist(null);
            }
        }

        return $this;
    }
    
    /**
    * Retourne les noms des catégories utilisées par les formations de la playlist.
    *
    * @return Collection<int, string> Liste des noms de catégories.
    */
    public function getCategoriesPlaylist() : Collection
    {
        $categories = new ArrayCollection();
        
        foreach ($this->formations as $formation) {
            $categoriesFormation = $formation->getCategories();
            
            foreach ($categoriesFormation as $categorieFormation) {
                if (!$categories->contains($categorieFormation->getName())) {
                    $categories[] = $categorieFormation->getName();
                }
            }
        }
        return $categories;
    }

    public function getLikePlay(): int
    {
        return $this->like_play;
    }

    public function setLikePlay(int $like_play): static
    {
        $this->like_play = $like_play;

        return $this;
    }

    public function getUnlikePlay(): int
    {
        return $this->unlike_play;
    }

    public function setUnlikePlay(int $unlike_play): static
    {
        $this->unlike_play = $unlike_play;

        return $this;
    }
    
    public function addLikePlay() :static
    {
    $this->like_play++;

    return $this;
    }
       
    public function addUnLikePlay() :static
    {
    $this->unlike_play++;

    return $this;
    }
}
