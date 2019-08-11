<?php declare(strict_types = 1);

namespace App\Message\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class Selection
{
    /**
     * @var int
     * @Assert\NotBlank(payload={"level"="selection"})
     */
    private $id;

    /**
     * @var float
     * @Assert\Range(min = 1, max = 100000, payload={"level"="selection"})
     */
    private $odds;

    /**
     * Selection constructor.
     *
     * @param int   $id
     * @param float $odds
     */
    public function __construct(int $id, float $odds)
    {
        $this->id = $id;
        $this->odds = $odds;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getOdds(): float
    {
        return $this->odds;
    }
}
