<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CategorieRepository::class)
 */
class Categorie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Evenement::class, mappedBy="idcategorie")
     */
    private $idevenement;

    public function __construct()
    {
        $this->idevenement = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection|Evenement[]
     */
    public function getIdevenement(): Collection
    {
        return $this->idevenement;
    }

    public function addIdevenement(Evenement $idevenement): self
    {
        if (!$this->idevenement->contains($idevenement)) {
            $this->idevenement[] = $idevenement;
            $idevenement->setIdcategorie($this);
        }

        return $this;
    }

    public function removeIdevenemnt(Evenement $idevenement): self
    {
        if ($this->idevenement->removeElement($idevenement)) {
            // set the owning side to null (unless already changed)
            if ($idevenement->getIdcategorie() === $this) {
                $idevenement->setIdcategorie(null);
            }
        }

        return $this;
    }
}
