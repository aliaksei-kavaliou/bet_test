<?php declare(strict_types = 1);

namespace App\Handler;

use App\Entity\Bet;
use App\Entity\BetSelection;
use App\Entity\Player;
use App\Message\MakeBetMessage;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class MakeBetHandler implements MessageHandlerInterface
{
    /** @var RegistryInterface */
    private $registry;

    /**
     * MakeBetHandler constructor.
     *
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param MakeBetMessage $message
     *
     * @return Bet
     * @throws \Exception
     */
    public function __invoke(MakeBetMessage $message): Bet
    {
        $objectManager = $this->registry->getManager();

        $player = $objectManager->find(Player::class, $message->getPlayerId());

        if (!$player) {
            $player = new Player();
            $player->setId($message->getPlayerId());
            $objectManager->persist($player);
            $metadata = $objectManager->getClassMetaData(Player::class);
            $metadata->setIdGeneratorType(ClassMetadata::GENERATOR_TYPE_NONE);
        }

        $bet = new Bet();
        $bet->setStakeAmount($message->getStakeAmount())
            ->setCreatedAt(new \DateTime());

        $player->addBet($bet);

        foreach ($message->getSelections() as $selection) {
            $betSelection = new BetSelection();
            $betSelection->setOdds((float)$selection->getOdds())
                ->setSelectionId($selection->getId());
            $objectManager->persist($betSelection);
            $bet->addBetSelection($betSelection);
        }

        $objectManager->persist($bet);
        $objectManager->merge($player);
        $objectManager->flush();

        return $bet;
    }

    public function testInvoke(): void
    {
    }
}
