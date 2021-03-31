<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 */
class Evenement
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
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="idevenement")
     */
    private $idcategorie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;


    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;


    /**
     * @ORM\Column(type="integer")
     */
    private $jaime;

    /**
     * @ORM\Column(type="integer")
     */
    private $jaimepas;



    /**
     * @ORM\Column(type="integer")
     */
    private $nbp;


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

    public function getIdcategorie(): ?Categorie
    {
        return $this->idcategorie;
    }

    public function setIdcategorie(?Categorie $idcategorie): self
    {
        $this->idcategorie = $idcategorie;
        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }


    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }


    public function getJaime(): ?int
    {
        return $this->jaime;
    }

    public function setJaime(int $jaime): self
    {
        $this->jaime = $jaime;

        return $this;
    }


    public function getJaimepas(): ?int
    {
        return $this->jaimepas;
    }

    public function setJaimepas(int $jaimepas): self
    {
        $this->jaimepas = $jaimepas;

        return $this;
    }

    public function getNbp(): ?int
    {
        return $this->nbp;
    }

    public function setNbp(int $nbp): self
    {
        $this->nbp = $nbp;

        return $this;
    }
}
