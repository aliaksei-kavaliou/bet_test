<?php declare(strict_types = 1);

namespace App\Middleware\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class CanBetStamp implements StampInterface
{
    /** @var int|null */
    private $userId;

    /** @var float|null */
    private $stakeAmount;

    /**
     * CanBetStamp constructor.
     *
     * @param int|null   $userId
     * @param float|null $stakeAmount
     */
    public function __construct(?int $userId, ?float $stakeAmount)
    {
        $this->userId = $userId;
        $this->stakeAmount = $stakeAmount;
    }

    /**
     * @return int|null
     */
    public function getUserId(): ?int
    {
        return $this->userId;
    }

    /**
     * @return float|null
     */
    public function getStakeAmount(): ?float
    {
        return $this->stakeAmount;
    }
}
