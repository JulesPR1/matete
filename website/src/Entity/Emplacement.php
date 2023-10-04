<?php

namespace App\Entity;

use App\Repository\EmplacementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=EmplacementRepository::class)
 */
class Emplacement
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez entrer une adresse pour votre emplacemement")
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez entrer un code postal pour votre emplacemement")
     */
    private $codePostal;

    
    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="emplacement")
     */
    private $lesAnnonces;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="lesEmplacements")
     * @ORM\JoinColumn(nullable=false)
     */
    private $leUser;

    public function __construct()
    {
        $this->lesAnnonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

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
            $lesAnnonce->setEmplacement($this);
        }

        return $this;
    }

    public function removeLesAnnonce(Annonce $lesAnnonce): self
    {
        if ($this->lesAnnonces->removeElement($lesAnnonce)) {
            // set the owning side to null (unless already changed)
            if ($lesAnnonce->getEmplacement() === $this) {
                $lesAnnonce->setEmplacement(null);
            }
        }

        return $this;
    }

    public function __toString() {
        return $this->adresse . " (" . $this->codePostal . ")";
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
