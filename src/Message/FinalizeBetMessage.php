<?php declare(strict_types = 1);

namespace App\Message;

use App\Entity\Bet;

class FinalizeBetMessage implements MessageInterface
{
    /** @var int */
    private $betId;

    /**
     * FinalizeBetMessage constructor.
     *
     * @param int $betId
     */
    public function __construct(int $betId)
    {
        $this->betId = $betId;
    }

    /**
     * @return int
     */
    public function getBetId(): int
    {
        return $this->betId;
    }
}
