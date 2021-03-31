<?php

namespace App\Entity;

use App\Repository\PostulerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostulerRepository::class)
 */
class Postuler
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Offre::class, inversedBy="likes")
     */
    private $offre;

    /**
     * @ORM\ManyToOne(targetEntity=Recruteur::class, inversedBy="likes")
     */
    private $recruteur;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $accepte;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOffre(): ?Offre
    {
        return $this->offre;
    }

    public function setOffre(?Offre $offre): self
    {
        $this->offre = $offre;

        return $this;
    }

    public function getRecruteur(): ?Recruteur
    {
        return $this->recruteur;
    }

    public function setRecruteur(?Recruteur $recruteur): self
    {
        $this->recruteur = $recruteur;

        return $this;
    }

    public function getAccepte(): ?string
    {
        return $this->accepte;
    }

    public function setAccepte(?string $accepte): self
    {
        $this->accepte = $accepte;

        return $this;
    }
}
