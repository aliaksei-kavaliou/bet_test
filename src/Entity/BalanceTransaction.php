<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BalanceTransactionRepository")
 */
class BalanceTransaction
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="decimal", scale=2)
     */
    private $amount;

    /**
     * @ORM\Column(type="decimal", scale=2, nullable=true)
     */
    private $amount_before;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Player", inversedBy="transactions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getAmountBefore(): ?float
    {
        return $this->amount_before;
    }

    public function setAmountBefore(?float $amount_before): self
    {
        $this->amount_before = $amount_before;

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
}
