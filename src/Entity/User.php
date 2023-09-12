<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
// Contraintes d'uniticité d'email et de pseudo
#[UniqueEntity(fields: ['email'], message: "L'email renseigné est déjà utilisé. Veuillez entrer un email différent.")]
#[UniqueEntity(fields: ['pseudo'], message: "Le pseudo renseigné est déjà utilisé. Veuillez entrer un pseudo différent.")]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\Regex(
        pattern: "/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/",
        message: "Votre mot de passe doit correspondre au format exigé."
    )]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 50, unique: true)]
    private ?string $pseudo = null;

    #[ORM\ManyToMany(targetEntity: Spot::class, inversedBy: 'favoritedByUsers')]
    private Collection $favoriteSpots;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'author', targetEntity: Spot::class)]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')]
    private Collection $addedSpots;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable:true, options:["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $registrationDate;

    #[ORM\Column]
    private ?bool $isBanned = false;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notation::class)]
    private Collection $notations; //ici je change "null" en false pour dire que mon user est "pas banni" par défaut.

    public function __construct()
    {
        $this->favoriteSpots = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->addedSpots = new ArrayCollection();
        $this->notations = new ArrayCollection();
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
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * @return Collection<int, Spot>
     */
    public function getFavoriteSpots(): Collection
    {
        return $this->favoriteSpots;
    }

    public function addFavoriteSpot(Spot $favoriteSpot): self
    {
        if (!$this->favoriteSpots->contains($favoriteSpot)) {
            $this->favoriteSpots->add($favoriteSpot);
        }

        return $this;
    }

    public function removeFavoriteSpot(Spot $favoriteSpot): self
    {
        $this->favoriteSpots->removeElement($favoriteSpot);

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setAuthor($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getAuthor() === $this) {
                $comment->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Spot>
     */
    public function getAddedSpots(): Collection
    {
        return $this->addedSpots;
    }

    public function addAddedSpot(Spot $addedSpot): self
    {
        if (!$this->addedSpots->contains($addedSpot)) {
            $this->addedSpots->add($addedSpot);
            $addedSpot->setAuthor($this);
        }

        return $this;
    }

    public function removeAddedSpot(Spot $addedSpot): self
    {
        if ($this->addedSpots->removeElement($addedSpot)) {
            // set the owning side to null (unless already changed)
            if ($addedSpot->getAuthor() === $this) {
                $addedSpot->setAuthor(null);
            }
        }

        return $this;
    }

    public function getRegistrationDate(): ?\DateTimeInterface
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate(\DateTimeInterface $registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }
    public function __toString(){
        return $this->pseudo;
    }

    public function isIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    /**
     * @return Collection<int, Notation>
     */
    public function getNotations(): Collection
    {
        return $this->notations;
    }

    public function addNotation(Notation $notation): self
    {
        if (!$this->notations->contains($notation)) {
            $this->notations->add($notation);
            $notation->setUser($this);
        }

        return $this;
    }

    public function removeNotation(Notation $notation): self
    {
        if ($this->notations->removeElement($notation)) {
            // set the owning side to null (unless already changed)
            if ($notation->getUser() === $this) {
                $notation->setUser(null);
            }
        }

        return $this;
    }
}
