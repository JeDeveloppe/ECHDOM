<?php

namespace App\Entity;

use App\Repository\RateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RateRepository::class)]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Notation $notation = null;

    #[ORM\ManyToOne(inversedBy: 'rates')]
    #[ORM\JoinColumn(nullable: false)]
    private ?NotationCriteria $criteria = null;

    #[ORM\Column]
    private ?int $score = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNotation(): ?Notation
    {
        return $this->notation;
    }

    public function setNotation(?Notation $notation): static
    {
        $this->notation = $notation;

        return $this;
    }

    public function getCriteria(): ?NotationCriteria
    {
        return $this->criteria;
    }

    public function setCriteria(?NotationCriteria $criteria): static
    {
        $this->criteria = $criteria;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): static
    {
        $this->score = $score;

        return $this;
    }
}
