<?php declare(strict_types = 1);

namespace App\Tests\unit\Middleware;

use App\Entity\Player;
use App\Exception\CanBetException;
use App\Message\MakeBetMessage;
use App\Middleware\CanBetMiddleware;
use App\Middleware\Stamp\CanBetStamp;
use App\Service\BetHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Stamp\ReceivedStamp;
use Symfony\Component\Messenger\Test\Middleware\MiddlewareTestCase;

class CanBetMiddlewareTest extends MiddlewareTestCase
{
    /** @var RegistryInterface */
    private $registry;

    /** @var EntityManagerInterface */
    private $em;

    /** @var BetHelper */
    private $helper;

    /** @var CanBetMiddleware */
    private $middleware;

    protected function setUp()
    {
        parent::setUp();
        $this->em = $this->prophesize(EntityManagerInterface::class);
        $this->registry = $this->prophesize(RegistryInterface::class);
        $this->registry->getManager()->willReturn($this->em->reveal());
        $this->helper = $this->prophesize(BetHelper::class);
        $this->middleware = new CanBetMiddleware($this->registry->reveal(), $this->helper->reveal());
    }

    public function testHandle(): void
    {
        $player = new Player();
        $this->em->find(Player::class, 1)->willReturn($player);
        $this->helper->playerHasEnoughMoney($player, 100.0)->willReturn(true);
        $this->helper->playerHasPendingOperation($player)->willReturn(false);

        $message = new MakeBetMessage(1, 100.0, [['id' => 1, 'odds' => '1.623']]);
        $envelop = new Envelope($message);
        $this->assertInstanceOf(
            Envelope::class,
            $this->middleware
                ->handle($envelop->with(new CanBetStamp(1, 100.0)), $this->getStackMock())
        );
    }

    /** @dataProvider exceptionDataProvider */
    public function testHandleException(
        $player,
        $stamp,
        $hasEnoughMoney,
        $hasPendingOperation,
        $expectedMessage
    ): void {
        $this->expectException(CanBetException::class);
        $this->expectExceptionMessage($expectedMessage);

        $this->em->find(Player::class, $stamp->getPlayerId())->willReturn($player);

        if (null === $hasEnoughMoney) {
            $this->helper->playerHasEnoughMoney($player, $stamp->getStakeAmount())->shouldNotBeCalled();
        } else {
            $this->helper->playerHasEnoughMoney($player, $stamp->getStakeAmount())->shouldBeCalled()
                ->willReturn($hasEnoughMoney);
        }

        if (null === $hasPendingOperation) {
            $this->helper->playerHasPendingOperation($player)->shouldNotBeCalled();
        } else {
            $this->helper->playerHasPendingOperation($player)->shouldBeCalled()->willReturn($hasPendingOperation);
        }

        $message = new MakeBetMessage(
            $stamp->getPlayerId(), $stamp->getStakeAmount(), [['id' => 1, 'odds' => '1.623']]
        );
        $envelop = new Envelope($message);
        $this->middleware->handle($envelop->with($stamp), $this->getStackMock(false));
    }

    public function exceptionDataProvider(): array
    {
        return [
            'no player, stake amount bigger than default' => [
                'player' => null,
                'stamp' => new CanBetStamp(null, 20000.0),
                'hasEnoughMoney' => null,
                'hasPendingOperation' => null,
                'expectedMessage' => 'Insufficient balance',
            ],
            'player, has not enough money' => [
                'player' => new Player(),
                'stamp' => new CanBetStamp(1, 2000.0),
                'hasEnoughMoney' => false,
                'hasPendingOperation' => null,
                'expectedMessage' => 'Insufficient balance',
            ],
            'player, has not pending operation' => [
                'player' => new Player(),
                'stamp' => new CanBetStamp(1, 2000.0),
                'hasEnoughMoney' => true,
                'hasPendingOperation' => true,
                'expectedMessage' => 'Your previous action is not finished yet',
            ],
        ];
    }
}
