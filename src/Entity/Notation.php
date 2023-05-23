<?php

namespace App\Entity;

use App\Repository\NotationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotationRepository::class)]
class Notation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'notations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Spot $spot = null;

    #[ORM\ManyToOne(inversedBy: 'notations')]
    private ?User $user = null;

    #[ORM\Column]
    private ?int $note = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSpot(): ?Spot
    {
        return $this->spot;
    }

    public function setSpot(?Spot $spot): self
    {
        $this->spot = $spot;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): self
    {
        $this->note = $note;

        return $this;
    }
}
