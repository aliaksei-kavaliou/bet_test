<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PlayerRepository")
 */
class Player
{
    public const DEFAULT_BALANCE = 1000;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $balance = self::DEFAULT_BALANCE;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Bet", mappedBy="player", cascade={"persist"})
     */
    private $bets;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BalanceTransaction", mappedBy="player", cascade={"persist"})
     */
    private $transactions;

    public function __construct()
    {
        $this->bets = new ArrayCollection();
        $this->transactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        if (null !== $this->id) {
            throw new \LogicException("User already has Id");
        }

        $this->id = $id;

        return $this;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): self
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * @return Collection|Bet[]
     */
    public function getBets(): Collection
    {
        return $this->bets;
    }

    public function addBet(Bet $bet): self
    {
        if (!$this->bets->contains($bet)) {
            $this->bets[] = $bet;
            $bet->setPlayer($this);
        }

        return $this;
    }

    public function removeBet(Bet $bet): self
    {
        if ($this->bets->contains($bet)) {
            $this->bets->removeElement($bet);
            // set the owning side to null (unless already changed)
            if ($bet->getPlayer() === $this) {
                $bet->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|BalanceTransaction[]
     */
    public function getTransactions()
    {
        return $this->transactions;
    }

    /**
     * @param BalanceTransaction $balanceTransaction
     *
     * @return Player
     */
    public function addTransaction(BalanceTransaction $balanceTransaction): self
    {
        if (!$this->transactions->contains($balanceTransaction)) {
            $this->transactions[] = $balanceTransaction;
            $balanceTransaction->setPlayer($this);
        }

        return $this;
    }

    public function removeTransaction(BalanceTransaction $balanceTransaction): self
    {
        if ($this->transactions->contains($balanceTransaction)) {
            $this->transactions->removeElement($balanceTransaction);
            // set the owning side to null (unless already changed)
            if ($balanceTransaction->getPlayer() === $this) {
                $balanceTransaction->setPlayer(null);
            }
        }

        return $this;
    }
}
