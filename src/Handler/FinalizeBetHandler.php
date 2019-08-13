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
    public function __construct(RegistryInterface $registry, int $sleepInterval = 0)
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
        $objectManager = $this->registry->getManager();

        $bet = $objectManager->find(Bet::class, $message->getBetId());

        if (!$bet) {
            return;
        }

        $bet->setStatus(Bet::STATUS_APPROVED);
        $player = $bet->getPlayer();
        $balanceBefore = $player->getBalance();
        $player->setBalance($balanceBefore - $bet->getStakeAmount());
        $balanceTransaction = new BalanceTransaction();
        $balanceTransaction->setAmountBefore($balanceBefore)
            ->setAmount($player->getBalance());

        $player->addTransaction($balanceTransaction);

        $objectManager->persist($bet);
        $objectManager->persist($balanceTransaction);
        $objectManager->persist($player);
        $objectManager->flush();
    }
}
