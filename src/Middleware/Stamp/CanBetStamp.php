<?php declare(strict_types = 1);

namespace App\Middleware\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class CanBetStamp implements StampInterface
{
    /** @var int|null */
    private $playerId;

    /** @var float|null */
    private $stakeAmount;

    /**
     * CanBetStamp constructor.
     *
     * @param int|null   $playerId
     * @param float|null $stakeAmount
     */
    public function __construct(?int $playerId, ?float $stakeAmount)
    {
        $this->playerId = $playerId;
        $this->stakeAmount = $stakeAmount;
    }

    /**
     * @return int|null
     */
    public function getPlayerId(): ?int
    {
        return $this->playerId;
    }

    /**
     * @return float|null
     */
    public function getStakeAmount(): ?float
    {
        return $this->stakeAmount;
    }
}
