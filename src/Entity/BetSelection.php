<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BetSelectionsRepository")
 */
class BetSelection
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", scale=3)
     */
    private $odds;

    /**
     * @ORM\Column(type="integer")
     */
    private $selectionId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Bet", inversedBy="betSelections")
     * @ORM\JoinColumn(nullable=false)
     */
    private $bet;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOdds(): ?float
    {
        return $this->odds;
    }

    public function setOdds(float $odds): self
    {
        $this->odds = $odds;

        return $this;
    }

    public function getSelectionId(): ?int
    {
        return $this->selectionId;
    }

    public function setSelectionId(int $selectionId): self
    {
        $this->selectionId = $selectionId;

        return $this;
    }

    public function getBet(): ?Bet
    {
        return $this->bet;
    }

    public function setBet(?Bet $bet): self
    {
        $this->bet = $bet;

        return $this;
    }
}
