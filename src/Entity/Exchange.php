<?php

namespace App\Entity;

use App\Repository\ExchangeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ExchangeRepository::class)]
class Exchange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'exchanges')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ExchangeStatus $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'exchangesProposed')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $proposer = null;

    #[ORM\ManyToOne(inversedBy: 'exchangesReceiver')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $receiver = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $startDate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $endDate = null;

    #[ORM\Column]
    private ?int $proposerTimeSavedMinutes = null;

    #[ORM\Column]
    private ?int $proposerCo2SavedGrams = null;

    #[ORM\Column(nullable: true)]
    private ?int $receiverTimeSavedMinutes = null;

    #[ORM\Column(nullable: true)]
    private ?int $receiverCo2SavedGrams = null;

    #[ORM\OneToOne(mappedBy: 'exchange', cascade: ['persist', 'remove'])]
    private ?Notation $notation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?ExchangeStatus
    {
        return $this->status;
    }

    public function setStatus(?ExchangeStatus $status): static
    {
        $this->status = $status;

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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getProposer(): ?User
    {
        return $this->proposer;
    }

    public function setProposer(?User $proposer): static
    {
        $this->proposer = $proposer;

        return $this;
    }

    public function getReceiver(): ?User
    {
        return $this->receiver;
    }

    public function setReceiver(?User $receiver): static
    {
        $this->receiver = $receiver;

        return $this;
    }

    public function getStartDate(): ?\DateTimeImmutable
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeImmutable $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeImmutable $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getProposerTimeSavedMinutes(): ?int
    {
        return $this->proposerTimeSavedMinutes;
    }

    public function setProposerTimeSavedMinutes(int $proposerTimeSavedMinutes): static
    {
        $this->proposerTimeSavedMinutes = $proposerTimeSavedMinutes;

        return $this;
    }

    public function getProposerCo2SavedGrams(): ?int
    {
        return $this->proposerCo2SavedGrams;
    }

    public function setProposerCo2SavedGrams(int $proposerCo2SavedGrams): static
    {
        $this->proposerCo2SavedGrams = $proposerCo2SavedGrams;

        return $this;
    }

    public function getReceiverTimeSavedMinutes(): ?int
    {
        return $this->receiverTimeSavedMinutes;
    }

    public function setReceiverTimeSavedMinutes(?int $receiverTimeSavedMinutes): static
    {
        $this->receiverTimeSavedMinutes = $receiverTimeSavedMinutes;

        return $this;
    }

    public function getReceiverCo2SavedGrams(): ?int
    {
        return $this->receiverCo2SavedGrams;
    }

    public function setReceiverCo2SavedGrams(?int $receiverCo2SavedGrams): static
    {
        $this->receiverCo2SavedGrams = $receiverCo2SavedGrams;

        return $this;
    }

    public function getNotation(): ?Notation
    {
        return $this->notation;
    }

    public function setNotation(Notation $notation): static
    {
        // set the owning side of the relation if necessary
        if ($notation->getExchange() !== $this) {
            $notation->setExchange($this);
        }

        $this->notation = $notation;

        return $this;
    }
}
