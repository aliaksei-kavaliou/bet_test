<?php declare(strict_types = 1);

namespace App\Message;

use App\Exception\Errors;
use App\Message\Dto\Selection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MakeBetMessage
{
    public const MAX_WIN_AMOUNT = 20000;
    /**
     * @var int
     * @Assert\Type(type="integer", payload={"level"="global"}, message=Errors::BAD_STRUCTURE)
     * @Assert\NotBlank(
     *     payload={"level"="global"},
     *     message=Errors::BAD_STRUCTURE
     * )
     */
    private $playerId;

    /**
     * @var mixed
     * @Assert\NotBlank(payload={"level"="global"}, message=Errors::BAD_STRUCTURE)
     * @Assert\Type(type="string", payload={"level"="global"}, message=Errors::BAD_STRUCTURE)
     * @Assert\Regex(pattern="/^(\d+(\.\d{1,2})?)$/", payload={"level"="global"}, message=Errors::BAD_STRUCTURE)
     * @Assert\Range(
     *     min=0.3,
     *     max=10000,
     *     minMessage=Errors::MINIMUM_STAKE_AMOUNT,
     *     maxMessage=Errors::MAXIMUM_STAKE_AMOUNT,
     *     payload={"level"="global"}
     * )
     */
    private $stakeAmount;

    /**
     * @var  Selection[]
     * @Assert\Type(type="array", payload={"level"="global"}, message=Errors::BAD_STRUCTURE)
     * @Assert\Count(
     *     min=1,
     *     max=20,
     *     payload={"level"="global"},
     *     minMessage=Errors::MINIMUM_SELECTIONS_NUMBER,
     *     maxMessage=Errors::MAXIMUM_SELECTIONS_NUMBER,
     * )
     * @Assert\Valid(payload={"level"="selections"}, traverse=true)
     */
    private $selections = [];

    /**
     * @Assert\Callback(payload = {"level"="selection"})
     * @param ExecutionContextInterface $context
     * @param                           $payload
     */
    public function validateSelections(ExecutionContextInterface $context, $payload): void
    {
        $ids = [];
        foreach ($this->selections as $selection) {
            $ids[] = $selection->getId();
        }

        if (count($ids) !== count(\array_unique($ids))) {
            $context->buildViolation(Errors::DUPLICATE_SELECTION)
                ->atPath('selections')
                ->setCode(Errors::DUPLICATE_SELECTION)
                ->addViolation();
        }
    }

    /**
     * @Assert\Callback(payload = {"level"="global"})
     * @param ExecutionContextInterface $context
     * @param                           $payload
     */
    public function validateMaxWin(ExecutionContextInterface $context, $payload): void
    {
        $win = $this->stakeAmount;
        foreach ($this->selections as $selection) {
            $win *= $selection->getOdds();
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
     * @param string|null $stakeAmount
     * @param array|null $selections
     */
    public function __construct($playerId, $stakeAmount, $selections)
    {
        $this->playerId = $playerId;
        $this->stakeAmount = $stakeAmount;

        if (!is_array($selections)) {
            return;
        }

        foreach ($selections as $selection) {
            $this->selections[] = new Selection($selection['id'] ?? null, $selection['odds'] ?? null);
        }
    }

    /**
     * @return int
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * @return mixed
     */
    public function getStakeAmount()
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
