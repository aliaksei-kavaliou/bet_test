<?php declare(strict_types = 1);

namespace App\Service;

use App\Entity\Bet;
use App\Entity\Player;
use Symfony\Bridge\Doctrine\RegistryInterface;

class BetHelper
{
    /**
     * BetHelper constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param Player $player
     * @param float  $stakeAmount
     *
     * @return bool
     */
    public function playerHasEnoughMoney(Player $player, float $stakeAmount): bool
    {
        return $player->getBalance() >= $stakeAmount;
    }

    /**
     * @param Player $player
     *
     * @return bool
     */
    public function playerHasPendingOperation(Player $player): bool
    {
        foreach ($player->getBets() as $bet) {
            if (Bet::STATUS_PENDING === $bet->getStatus()) {
                return true;
            }
        }

        return false;
    }
}
