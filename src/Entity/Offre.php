<?php

namespace App\Entity;
use App\Entity\Recruteur;
use App\Repository\OffreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use http\Env\Request;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass=OffreRepository::class)
 *
 */
class Offre
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message=" this field is required ")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message=" this field should be a valid email ")
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255)
     *@Assert\NotBlank(message=" this field is required ")
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message=" this field is required ")
     */
    private $description;
    protected $captchaCode;
    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="idoffre")
     */
    private $idcategoriy;

    /**
     * @ORM\ManyToMany(targetEntity=Recruteur::class, mappedBy="offre")
     */
    private $recruteurs;

    /**
     * @ORM\Column(type="integer")
     */
    private $abn;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="offre")
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Postuler::class, mappedBy="offre")
     */
    private $likes;

    public function __construct()
    {
        $this->recruteurs = new ArrayCollection();
        $this->candidats = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
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

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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
    public function getIdcategoriy(): ?Categorie
    {
        return $this->idcategoriy;
    }

    public function setIdcategoriy(?Categorie $idcategoriy): self
    {
        $this->idcategoriy = $idcategoriy;
        return $this;
    }
    public function getCaptchaCode()
    {
        return $this->captchaCode;
    }

    public function setCaptchaCode($captchaCode)
    {
        $this->captchaCode = $captchaCode;
    }

    /**
     * @return Collection|Recruteur[]
     */
    public function getRecruteurs(): Collection
    {
        return $this->recruteurs;
    }

    public function addRecruteur(Recruteur $recruteur): self
    {
        if (!$this->recruteurs->contains($recruteur)) {
            $this->recruteurs[] = $recruteur;
            $recruteur->addOffre($this);
        }

        return $this;
    }

    public function removeRecruteur(Recruteur $recruteur): self
    {
        if ($this->recruteurs->removeElement($recruteur)) {
            $recruteur->removeOffre($this);
        }

        return $this;
    }
    public function getAbn(): ?int
    {
        return $this->abn;
    }
    public function setAbn(int $abn): self
    {
        $this->abn = $abn;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setOffre($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getOffre() === $this) {
                $comment->setOffre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Postuler[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Postuler $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setOffre($this);
        }

        return $this;
    }

    public function removeLike(Postuler $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getOffre() === $this) {
                $like->setOffre(null);
            }
        }

        return $this;
    }

    public function isLikedByRecruteur(Recruteur $recruteur) : bool
    {
       foreach ($this->likes as $like)
       {
           if($like->getRecruteur()==$recruteur)
               return true;
       }
       return false ;
    }
}
