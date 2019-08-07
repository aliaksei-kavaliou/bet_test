<?php declare(strict_types = 1);

namespace App\Message;

use App\Entity\Bet;

class FinalizeBetMessage implements MessageInterface
{
    /** @var Bet */
    private $bet;

    /**
     * FinalizeBetMessage constructor.
     *
     * @param Bet $bet
     */
    public function __construct(Bet $bet)
    {
        $this->bet = $bet;
    }

    /**
     * @return Bet
     */
    public function getBet(): Bet
    {
        return $this->bet;
    }
}
