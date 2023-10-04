<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank(message = "Veuillez entrer un nom de catégorie")
     * 
     */
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="laCategorie")
     */
    private $lesAnnonces;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $color;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $seuilmini;

    public function __construct()
    {
        $this->lesAnnonces = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Annonce[]
     */
    public function getLesAnnonces(): Collection
    {
        return $this->lesAnnonces;
    }

    public function addLesAnnonce(Annonce $lesAnnonce): self
    {
        if (!$this->lesAnnonces->contains($lesAnnonce)) {
            $this->lesAnnonces[] = $lesAnnonce;
            $lesAnnonce->setLaCategorie($this);
        }

        return $this;
    }

    public function removeLesAnnonce(Annonce $lesAnnonce): self
    {
        if ($this->lesAnnonces->removeElement($lesAnnonce)) {
            // set the owning side to null (unless already changed)
            if ($lesAnnonce->getLaCategorie() === $this) {
                $lesAnnonce->setLaCategorie(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->nom;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(?string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getSeuilmini(): ?int
    {
        return $this->seuilmini;
    }

    public function setSeuilmini(?int $seuilmini): self
    {
        $this->seuilmini = $seuilmini;

        return $this;
    }
}
