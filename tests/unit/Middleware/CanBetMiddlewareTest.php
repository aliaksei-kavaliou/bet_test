<?php declare(strict_types = 1);

namespace App\Tests\unit\Middleware;

use App\Entity\Player;
use App\Message\MakeBetMessage;
use App\Middleware\CanBetMiddleware;
use App\Middleware\Stamp\CanBetStamp;
use App\Service\BetHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Messenger\Envelope;
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
        $this->helper->playerHasEnoughMoney()->willReturn(true);
        $this->helper->playerHasPendingOperation($player)->willReturn(false);

        $message = new MakeBetMessage(1, 100.0, [['id' => 1, 'odds' => '1.623']]);
        $envelop = new Envelope($message);
        $this->assertInstanceOf(
            Envelope::class,
            $this->middleware->handle($envelop, $this->getStackMock())->with(new CanBetStamp(1, 100.0))
        );
    }
}
