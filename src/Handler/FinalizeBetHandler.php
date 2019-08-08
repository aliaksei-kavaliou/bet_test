<?php declare(strict_types = 1);

namespace App\Handler;

use App\Entity\BalanceTransaction;
use App\Entity\Bet;
use App\Message\FinalizeBetMessage;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FinalizeBetHandler implements MessageHandlerInterface
{
    /** @var RegistryInterface */
    private $registry;

    /** @var int */
    private $sleepInterval;

    /**
     * FinalizeBetHandler constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry, int $sleepInterval = 30)
    {
        $this->registry = $registry;
        $this->sleepInterval = $sleepInterval;
    }

    /**
     * @param FinalizeBetMessage $message
     */
    public function __invoke(FinalizeBetMessage $message): void
    {
        sleep($this->sleepInterval);

        $bet = $message->getBet();
        $bet->setStatus(Bet::STATUS_APPROVED);
        $objectManager = $this->registry->getManager();

        $player = $bet->getPlayer();
        $balanceBefore = $player->getBalance();
        $player->setBalance($balanceBefore - $bet->getStakeAmount());
        $balanceTransaction = new BalanceTransaction();
        $balanceTransaction->setPlayer($player)
            ->setAmountBefore($balanceBefore)
            ->setAmount($player->getBalance());

        $objectManager->persist($balanceTransaction);
        $objectManager->merge($bet);
        $objectManager->merge($player);
        $objectManager->flush();
    }
}
