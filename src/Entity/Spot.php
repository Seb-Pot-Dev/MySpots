<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SpotRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: SpotRepository::class)]
class Spot
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $adress = null;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $cp = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column]
    private ?float $lat = null;

    #[ORM\Column]
    private ?float $lng = null;

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'favoriteSpots')]
    private Collection $favoritedByUsers;

    #[ORM\ManyToMany(targetEntity: Module::class, inversedBy: 'spots')]
    private Collection $modules;

    #[ORM\OneToMany(mappedBy: 'spotConcerned', targetEntity: Comment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'addedSpots')]
    #[ORM\JoinColumn(nullable: true, onDelete: 'SET NULL')] // L'attribut est nulable et passe NULL si il est supprimé 
    private ?User $author = null ;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, options:["default"=>"CURRENT_TIMESTAMP"])]
    private ?\DateTimeInterface $creationDate;

    #[ORM\Column(options:["default"])]
    private ?bool $isValidated = null;

    #[ORM\OneToMany(mappedBy: 'spot', targetEntity: Notation::class, orphanRemoval: true, cascade:['persist'])]
    private Collection $notations;

    #[ORM\OneToMany(mappedBy: 'spot', targetEntity: Picture::class, orphanRemoval: true, cascade:['persist'])]
    private Collection $pictures;

    #[ORM\Column]
    private ?bool $covered = null;

    #[ORM\Column]
    private ?bool $official = null;

    public function __construct()
    {
        $this->favoritedByUsers = new ArrayCollection();
        $this->modules = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->notations = new ArrayCollection();
        $this->pictures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAdress(): ?string
    {
        return $this->adress;
    }

    public function setAdress(?string $adress): self
    {
        $this->adress = $adress;

        return $this;
    }

    public function getCp(): ?string
    {
        return $this->cp;
    }

    public function setCp(?string $cp): self
    {
        $this->cp = $cp;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getFavoritedByUsers(): Collection
    {
        return $this->favoritedByUsers;
    }

    public function addFavoritedByUser(User $favoritedByUser): self
    {
        if (!$this->favoritedByUsers->contains($favoritedByUser)) {
            $this->favoritedByUsers->add($favoritedByUser);
            $favoritedByUser->addFavoriteSpot($this);
        }

        return $this;
    }

    public function removeFavoritedByUser(User $favoritedByUser): self
    {
        if ($this->favoritedByUsers->removeElement($favoritedByUser)) {
            $favoritedByUser->removeFavoriteSpot($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Module>
     */
    public function getModules(): Collection
    {
        return $this->modules;
    }

    public function addModule(Module $module): self
    {
        if (!$this->modules->contains($module)) {
            $this->modules->add($module);
        }

        return $this;
    }

    public function removeModule(Module $module): self
    {
        $this->modules->removeElement($module);

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
            $comment->setSpotConcerned($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getSpotConcerned() === $this) {
                $comment->setSpotConcerned(null);
            }
        }

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }
    // find all spot from user
    /* */

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }
    // ici je modifie isIsValidated en getIsValidated
    public function getIsValidated(): ?bool
    {
        return $this->isValidated;
    }

    public function setIsValidated(bool $isValidated): self
    {
        $this->isValidated = $isValidated;

        return $this;
    }
    public function __toString(){
        return $this->name;
    }

    public function getAvgNote(): ?float
    {
        $avg = null;
$note = 0;
$nbNotation = count($this->notations);

if ($nbNotation > 0) {
    foreach ($this->notations as $notation) {
        $note += $notation->getNote();
    }
    $avg = $note / $nbNotation;
}

return $avg;

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
            $notation->setSpot($this);
        }

        return $this;
    }

    public function removeNotation(Notation $notation): self
    {
        if ($this->notations->removeElement($notation)) {
            // set the owning side to null (unless already changed)
            if ($notation->getSpot() === $this) {
                $notation->setSpot(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures->add($picture);
            $picture->setSpot($this);
        }

        return $this;
    }

    public function removePicture(Picture $picture): self
    {
        if ($this->pictures->removeElement($picture)) {
            // set the owning side to null (unless already changed)
            if ($picture->getSpot() === $this) {
                $picture->setSpot(null);
            }
        }

        return $this;
    }

    public function isCovered(): ?bool
    {
        return $this->covered;
    }

    public function setCovered(bool $covered): self
    {
        $this->covered = $covered;

        return $this;
    }

    public function isOfficial(): ?bool
    {
        return $this->official;
    }

    public function setOfficial(bool $official): self
    {
        $this->official = $official;

        return $this;
    }
}