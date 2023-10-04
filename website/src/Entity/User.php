<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
* @ORM\Entity(repositoryClass=UserRepository::class)
* @UniqueEntity("username", message = "Nom d'utilisateur déja utilisé")
* @UniqueEntity("email", message = "Adresse email déja utilisée")
*/
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     *     message = "Ce n'est pas un email valide"
     * )
     * @Assert\NotBlank(message = "Veuillez entrer votre adresse email")
    */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    public $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(
     *      min = 8,
     *      max = 50,
     *      minMessage = "Votre mot de passe doit faire au moins 8 caractères",
     *      maxMessage = "Votre mot de passe ne doit pas faire plus de 50 caractères"
     * )
     * @Assert\NotBlank(message = "Veuillez entrer un mot de passe d'au moins 8 caractères")
    */
    private $password;
    
    /**
     * @Assert\EqualTo(propertyPath="password",message = "La confirmation ne correspond pas")
     * @Assert\NotBlank(message = "Veuillez confirmer votre mot de passe")
    */
    public $confirm_password;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * @Assert\NotBlank(message = "Veuillez entrer un nom d'utilisateur")
     * @Assert\Length(
     *      min = 5,
     *      max = 12,
     *      minMessage = "Votre nom d'utilisateur ne doit pas faires moins de 5 caractères",
     *      maxMessage = "Votre nom d'utilisateur ne doit pas faires plus de 12 caractères"
     * )
    */
    private $username;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    public $suspendu;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message = "Veuillez entrer votre numéro de téléphone")
     */
    private $numTel;

    /**
     * @ORM\OneToMany(targetEntity=Emplacement::class, mappedBy="leUser")
     */
    public $lesEmplacements;

    /**
     * @ORM\OneToMany(targetEntity=Annonce::class, mappedBy="leUser")
     */
    public $lesAnnonces;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    public function __construct()
    {
        $this->lesEmplacements = new ArrayCollection();
        $this->lesAnnonces = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole($role){
        array_push($this->roles, $role);
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function setUsername(string $username = null): self
    {
        $this->username = $username;

        return $this;
    }

    public function isAdmin(): bool
    {
        return in_array("ROLE_ADMIN", $this->roles);
    }

    public function getSuspendu(): ?bool
    {
        return $this->suspendu;
    }

    public function setSuspendu(?bool $suspendu): self
    {
        $this->suspendu = $suspendu;

        return $this;
    }

    public function getNumTel(): ?string
    {
        return $this->numTel;
    }

    public function setNumTel(string $numTel): self
    {
        $this->numTel = $numTel;

        return $this;
    }

    /**
     * @return Collection|Emplacement[]
     */
    public function getLesEmplacements(): Collection
    {
        return $this->lesEmplacements;
    }

    public function addLesEmplacement(Emplacement $lesEmplacement): self
    {
        if (!$this->lesEmplacements->contains($lesEmplacement)) {
            $this->lesEmplacements[] = $lesEmplacement;
            $lesEmplacement->setLeUser($this);
        }

        return $this;
    }

    public function removeLesEmplacement(Emplacement $lesEmplacement): self
    {
        if ($this->lesEmplacements->removeElement($lesEmplacement)) {
            // set the owning side to null (unless already changed)
            if ($lesEmplacement->getLeUser() === $this) {
                $lesEmplacement->setLeUser(null);
            }
        }

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
            $lesAnnonce->setLeUser($this);
        }

        return $this;
    }

    public function removeLesAnnonce(Annonce $lesAnnonce): self
    {
        if ($this->lesAnnonces->removeElement($lesAnnonce)) {
            // set the owning side to null (unless already changed)
            if ($lesAnnonce->getLeUser() === $this) {
                $lesAnnonce->setLeUser(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->username;
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }
}