<?php declare(strict_types = 1);

namespace App\Tests\unit\Handler;

use App\Entity\Bet;
use App\Entity\BetSelection;
use App\Entity\Player;
use App\Handler\MakeBetHandler;
use App\Message\MakeBetMessage;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Bridge\Doctrine\RegistryInterface;

class MakeBetHandlerTest extends TestCase
{
    /** @var RegistryInterface */
    private $registry;

    /** @var EntityManagerInterface */
    private $em;

    /** @var MakeBetHandler */
    private $handler;

    protected function setUp()
    {
        parent::setUp();
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->registry->getManager()->willReturn($this->em->reveal())->shouldBeCalled();
        $this->handler = new MakeBetHandler($this->registry->reveal());

        $tester = $this;
        $this->em->merge(Argument::type(Player::class))->shouldBeCalled()->will(
            function ($args) use ($tester) {
                $player = $args[0];
                $tester->assertCount(1, $player->getBets());
            }
        );
        $this->em->persist(Argument::type(Bet::class))->shouldBeCalled();
        $this->em->persist(Argument::type(BetSelection::class))->shouldBeCalledTimes(2);
        $this->em->flush()->shouldBeCalled();

    }

    public function testInvokeNoPlayer(): void
    {
        $tester = $this;
        $this->em->getClassMetadata(Player::class)->shouldBeCalled()
            ->willReturn($this->prophesize(ClassMetadata::class)->reveal());
        $this->em->find(Player::class, 1)->shouldBeCalled()->willReturn(null);
        $this->em->persist(Argument::type(Player::class))->shouldBeCalled()->will(
            function ($args) use ($tester) {
                $player = $args[0];
                $tester->assertCount(0, $player->getBets());
            }
        );

        $message = new MakeBetMessage(1, '100.0', [['id' => 1, 'odds' => '1.623'], ['id' => 2, 'odds' => '1.623']]);
        $result = $this->handler->__invoke($message);
        $this->assertInstanceOf(Bet::class, $result);
        $this->assertCount(2, $result->getBetSelections());
    }

    public function testInvoke(): void
    {
        $this->em->getClassMetadata(Player::class)->shouldNotBeCalled();
        $player = new Player();
        $this->em->find(Player::class, 1)->shouldBeCalled()->willReturn($player);
        $this->em->persist(Argument::type(Player::class))->shouldNotBeCalled();
        $message = new MakeBetMessage(1, '100.0', [['id' => 1, 'odds' => '1.623'], ['id' => 2, 'odds' => '1.623']]);
        $result = $this->handler->__invoke($message);
        $this->assertInstanceOf(Bet::class, $result);
        $this->assertSame($player, $result->getPlayer());
    }
}
