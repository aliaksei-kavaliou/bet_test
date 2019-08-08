<?php declare(strict_types = 1);

namespace App\Tests\unit\Handler;

use App\Entity\BalanceTransaction;
use App\Entity\Bet;
use App\Entity\Player;
use App\Handler\FinalizeBetHandler;
use App\Message\FinalizeBetMessage;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\RegistryInterface;

class FinalizeBetTest extends TestCase
{
    /** @var RegistryInterface */
    private $registry;

    /** @var EntityManagerInterface */
    private $em;

    /** @var FinalizeBetHandler */
    private $handler;

    protected function setUp()
    {
        parent::setUp();
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->registry->getManager()->willReturn($this->em->reveal())->shouldBeCalled();
        $this->handler = new FinalizeBetHandler($this->registry->reveal(), 0);
    }

    public function testInvoke(): void
    {
        $player = new Player();
        $player->setBalance(100);
        $bet = new Bet();
        $bet->setStatus(Bet::STATUS_PENDING)
            ->setStakeAmount(60)
            ->setPlayer($player);
        $message = new FinalizeBetMessage($bet);

        $this->em->merge($player)->shouldBeCalled();
        $this->em->merge($bet)->shouldBeCalled();
        $this->em->persist(Argument::type(BalanceTransaction::class))->shouldBeCalled();
        $this->em->flush()->shouldBeCalled();

        $this->handler->__invoke($message);
        $this->assertEquals(40, $player->getBalance());
        $this->assertEquals(Bet::STATUS_APPROVED, $bet->getStatus());
    }
}
