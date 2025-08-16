<?php

namespace App\Entity;

use App\Repository\NotationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: NotationRepository::class)]
class Notation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'comment', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Exchange $exchange = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToOne(inversedBy: 'notationRater', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $rater = null;

    #[ORM\OneToOne(inversedBy: 'notationRated', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $ratedUser = null;

    /**
     * @var Collection<int, Rate>
     */
    #[ORM\OneToMany(targetEntity: Rate::class, mappedBy: 'notation')]
    private Collection $rates;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getExchange(): ?Exchange
    {
        return $this->exchange;
    }

    public function setExchange(Exchange $exchange): static
    {
        $this->exchange = $exchange;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getRater(): ?User
    {
        return $this->rater;
    }

    public function setRater(User $rater): static
    {
        $this->rater = $rater;

        return $this;
    }

    public function getRatedUser(): ?User
    {
        return $this->ratedUser;
    }

    public function setRatedUser(User $ratedUser): static
    {
        $this->ratedUser = $ratedUser;

        return $this;
    }

    /**
     * @return Collection<int, Rate>
     */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    public function addRate(Rate $rate): static
    {
        if (!$this->rates->contains($rate)) {
            $this->rates->add($rate);
            $rate->setNotation($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): static
    {
        if ($this->rates->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getNotation() === $this) {
                $rate->setNotation(null);
            }
        }

        return $this;
    }
}
