<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BetRepository")
 */
class Bet
{
    public const STATUS_PENDING = "pending";
    public const STATUS_APPROVED = "approved";

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $stakeAmount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\OneToMany(targetEntity="BetSelection", mappedBy="bet", orphanRemoval=true)
     */
    private $betSelections;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="bets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $status = self::STATUS_PENDING;

    public function __construct()
    {
        $this->betSelections = new ArrayCollection();
        $this->createdAt = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStakeAmount(): ?float
    {
        return $this->stakeAmount;
    }

    public function setStakeAmount(float $stakeAmount): self
    {
        $this->stakeAmount = $stakeAmount;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection|BetSelection[]
     */
    public function getBetSelections(): Collection
    {
        return $this->betSelections;
    }

    public function addBetSelection(BetSelection $betSelection): self
    {
        if (!$this->betSelections->contains($betSelection)) {
            $this->betSelections[] = $betSelection;
            $betSelection->setBet($this);
        }

        return $this;
    }

    public function removeBetSelection(BetSelection $betSelection): self
    {
        if ($this->betSelections->contains($betSelection)) {
            $this->betSelections->removeElement($betSelection);
            // set the owning side to null (unless already changed)
            if ($betSelection->getBet() === $this) {
                $betSelection->setBet(null);
            }
        }

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
