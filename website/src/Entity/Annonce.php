<?php

namespace App\Entity;

use App\Repository\AnnonceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnnonceRepository::class)
 */
class Annonce
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 4,
     *      max = 20,
     *      minMessage = "Le nom de l'annonce doit faire au moins 4 caractères",
     *      maxMessage = "Le nom de l'annonce ne doit pas faire plus de 20 caractères"
     * )
     * @Assert\NotBlank(message = "Veuillez entrer un nom pour votre annonce")
    */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      min = 8,
     *      max = 100,
     *      minMessage = "La description de l'annonce doit faire au moins 8 caractères",
     *      maxMessage = "La description de l'annonce ne doit pas faire plus de 100 caractères"
     * )
     * @Assert\NotBlank(message = "Veuillez entrer une description pour votre annonce")
    */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Positive(message = "La quantité doit être un nombre positif")
     * @Assert\NotBlank(message = "Veuillez entrer une quantité")
    */
    private $quantite;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *      max = 254,
     *      maxMessage = "Le lien de l'image est trop grand"
     * )
    */
    private $image;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Categorie::class, inversedBy="lesAnnonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $laCategorie;

   
    /**
     * @ORM\ManyToOne(targetEntity=Emplacement::class, inversedBy="lesAnnonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $emplacement;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="lesAnnonces")
     * @ORM\JoinColumn(nullable=false)
     */
    private $leUser;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantite(): ?string
    {
        return $this->quantite;
    }

    public function setQuantite(string $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getLaCategorie(): ?Categorie
    {
        return $this->laCategorie;
    }

    public function setLaCategorie(?Categorie $laCategorie): self
    {
        $this->laCategorie = $laCategorie;

        return $this;
    }

  
    public function getEmplacement(): ?Emplacement
    {
        return $this->emplacement;
    }

    public function setEmplacement(?Emplacement $emplacement): self
    {
        $this->emplacement = $emplacement;

        return $this;
    }

    public function getLeUser(): ?User
    {
        return $this->leUser;
    }

    public function setLeUser(?User $leUser): self
    {
        $this->leUser = $leUser;

        return $this;
    }
}
