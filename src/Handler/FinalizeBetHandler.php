<?php declare(strict_types = 1);

namespace App\Handler;

use App\Entity\Bet;
use App\Message\FinalizeBetMessage;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class FinalizeBetHandler implements MessageHandlerInterface
{
    /** @var RegistryInterface */
    private $registry;

    /**
     * FinalizeBetHandler constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param FinalizeBetMessage $message
     */
    public function __invoke(FinalizeBetMessage $message): void
    {
        sleep(30);

        $bet = $message->getBet();
        $bet->setStatus(Bet::STATUS_APPROVED);
        $objectManager = $this->registry->getManager();
        $objectManager->merge($bet);
        $objectManager->flush();
    }
}
