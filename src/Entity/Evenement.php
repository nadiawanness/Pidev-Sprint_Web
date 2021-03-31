<?php

namespace App\Entity;

use App\Repository\EvenementRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=EvenementRepository::class)
 * @UniqueEntity("email" , message="cet email existe deja")
 * @UniqueEntity("nom" , message="ce nom existe deja")
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
     * @Assert\NotBlank(message="Le nom est obligatoire")
     * @Assert\Length(min="3" , max="15")
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity=Cat::class, inversedBy="idevenement")
     */
    private $idcat;


    /**
     * @ORM\Column(type="datetime")
     * @Assert\Date()
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="description est obligatoire")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="email est obligatoire")
     * @Assert\Email(
     *     message = "The email '{{ value }}' is not a valid email."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jaime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $jaimepas;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbp;

    /**
     * @ORM\ManyToOne(targetEntity=Recruteur::class, inversedBy="idevenement")
     */
    private $idrecruteur;




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



    public function getIdcat(): ?Cat
    {
        return $this->idcat;
    }

    public function setIdcat(?Cat $idcat): self
    {
        $this->idcat = $idcat;
        return $this;
    }

    public function __construct()
    {
        $this->date = new \DateTime('now');
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

    public function setJaime(?int $jaime): self
    {
        $this->jaime = $jaime;

        return $this;
    }

    public function getJaimepas(): ?int
    {
        return $this->jaimepas;
    }

    public function setJaimepas(?int $jaimepas): self
    {
        $this->jaimepas = $jaimepas;

        return $this;
    }

    public function getNbp(): ?int
    {
        return $this->nbp;
    }

    public function setNbp(?int $nbp): self
    {
        $this->nbp = $nbp;

        return $this;
    }

    public function getIdrecruteur(): ?Recruteur
    {
        return $this->idrecruteur;
    }

    public function setIdrecruteur(?Recruteur $idrecruteur): self
    {
        $this->idrecruteur = $idrecruteur;
        return $this;
    }

}
