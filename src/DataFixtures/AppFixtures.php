<?php

namespace App\DataFixtures;

use App\Entity\Bet;
use App\Entity\Player;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public const NO_PENDING_OPERATION_PLAYER = "no_pending_operation_player";
    public const PENDING_OPERATION_PLAYER = "pending_operation_player";

    public function load(ObjectManager $manager)
    {
        $player1 = new Player();
        $manager->persist($player1);
        $this->addReference(self::NO_PENDING_OPERATION_PLAYER, $player1);

        $player2 = new Player();
        $bet = new Bet();
        $bet->setStakeAmount(50.0)->setPlayer($player2);
        $manager->persist($player2);
        $manager->persist($bet);
        $this->addReference(self::PENDING_OPERATION_PLAYER, $player2);

        $manager->flush();
    }
}
