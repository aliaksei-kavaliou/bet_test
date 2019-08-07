<?php declare(strict_types = 1);

namespace App\Message;

use App\Exception\Errors;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MakeBetMessage
{
    public const MAX_WIN_AMOUNT = 20000;
    /**
     * @var int
     * @Assert\NotBlank(message="User_id is mandatory field", payload={"level"="global"})
     */
    private $playerId;

    /**
     * @var float
     * @Assert\NotBlank(payload={"level"="global"})
     * @Assert\Range(
     *     min = 0.3,
     *     max = 10000,
     *     minMessage=Errors::MINIMUM_STAKE_AMOUNT,
     *     maxMessage=Errors::MAXIMUM_STAKE_AMOUNT,
     *     payload={"level"="global"}
     * )
     */
    private $stakeAmount;

    /**
     * @var  array
     * @Assert\NotBlank(payload={"level"="global"})
     * @Assert\Count(min = 1, max = 20, payload={"level"="global"}),
     * @Assert\All(
     *     @Assert\Collection(
     *          fields = {
     *              "id" = {
     *                  @Assert\NotBlank(payload={"level"="selection"}),
     *              },
     *              "odds" = {
     *                  @Assert\Range(min = 1, max = 100000, payload={"level"="selection"})
     *              }
     *          }
     *     )
     * )
     */
    private $selections;

    /**
     * @Assert\Callback(payload={"level"="selection"})
     * @param ExecutionContextInterface $context
     * @param                           $payload
     */
    public function validateSelections(ExecutionContextInterface $context, $payload): void
    {
        $ids = \array_column($this->selections, 'id');

        if (count($ids) !== count(\array_unique($ids))) {
            $context->buildViolation(Errors::DUPLICATE_SELECTION)
                ->atPath('selections')
                ->setCode(Errors::DUPLICATE_SELECTION)
                ->addViolation();
        }
    }

    /**
     * @Assert\Callback(payload={"level"="global"})
     * @param ExecutionContextInterface $context
     * @param                           $payload
     */
    public function validateMaxWin(ExecutionContextInterface $context, $payload): void
    {
        $win = $this->stakeAmount;
        foreach ($this->selections as $selection) {
            $win *= $selection['odds'];
        }

        if ($this->stakeAmount > self::MAX_WIN_AMOUNT) {
            $context->buildViolation(Errors::MAX_WIN_AMOUNT)
                ->atPath('selections')
                ->setParameter('{{ limit }}', self::MAX_WIN_AMOUNT)
                ->setCode(Errors::MAX_WIN_AMOUNT)
                ->addViolation();
        }
    }

    /**
     * MakeBetMessage constructor.
     *
     * @param int|null   $playerId
     * @param float|null $stakeAmount
     * @param array|null $selections
     */
    public function __construct(?int $playerId, ?float $stakeAmount, ?array $selections)
    {
        $this->playerId = $playerId;
        $this->stakeAmount = $stakeAmount;
        $this->selections = $selections;
    }

    /**
     * @return int
     */
    public function getPlayerId(): int
    {
        return $this->playerId;
    }

    /**
     * @return float
     */
    public function getStakeAmount(): float
    {
        return $this->stakeAmount;
    }

    /**
     * @return array
     */
    public function getSelections(): array
    {
        return $this->selections;
    }
}
