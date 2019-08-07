<?php declare(strict_types = 1);

namespace App\Middleware;

use App\Entity\Player;
use App\Exception\CanBetException;
use App\Exception\Errors;
use App\Middleware\Stamp\CanBetStamp;
use App\Service\BetHelper;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Middleware\MiddlewareInterface;
use Symfony\Component\Messenger\Middleware\StackInterface;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;

class CanBetMiddleware implements MiddlewareInterface
{
    /** @var RegistryInterface */
    private $registry;

    /** @var BetHelper */
    private $betHelper;

    /**
     * CanBetMiddleware constructor.
     *
     * @param RegistryInterface $registry
     * @param BetHelper         $betHelper
     */
    public function __construct(RegistryInterface $registry, BetHelper $betHelper)
    {
        $this->registry = $registry;
        $this->betHelper = $betHelper;
    }

    /** @inheritDoc */
    public function handle(Envelope $envelope, StackInterface $stack): Envelope
    {
        /** @var CanBetStamp $stamp */
        $stamp = $envelope->last(CanBetStamp::class);
        $isReceivedMessage = null !== $envelope->last(ReceivedStamp::class);

        if ($isReceivedMessage || !$stamp) {
            return $stack->next()->handle($envelope, $stack);
        }

        $user = $this->registry->getManager()->find(Player::class, $stamp->getUserId());

        if ((!$user && Player::DEFAULT_BALANCE < $stamp->getStakeAmount())
            || ($user && !$this->betHelper->playerHasEnoughMoney($user, $stamp->getStakeAmount()))
        ) {
            throw new CanBetException(
                Errors::$errorMessages[Errors::INSUFFICIENT_BALANCE],
                Errors::INSUFFICIENT_BALANCE
            );
        }

        if ($user && $this->betHelper->playerHasPendingOperation($user)) {
            throw new CanBetException(Errors::$errorMessages[Errors::NOT_FINISHED_ACTION], Errors::NOT_FINISHED_ACTION);
        }

        return $stack->next()->handle($envelope, $stack);
    }
}
