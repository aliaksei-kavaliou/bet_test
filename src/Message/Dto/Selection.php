<?php declare(strict_types = 1);

namespace App\Message\Dto;

use Symfony\Component\Validator\Constraints as Assert;
use App\Exception\Errors;

class Selection
{
    /**
     * @var int
     * @Assert\NotBlank(payload={"level"="selection"}, message=Errors::BAD_STRUCTURE)
     * @Assert\Type(type="integer", message=Errors::BAD_STRUCTURE, payload={"level"="selection"})
     */
    private $id;

    /**
     * @var mixed
     * @Assert\Range(
     *     min=1,
     *     max=100000,
     *     payload={"level"="selection"},
     *     minMessage=Errors::MINIMUM_ODDS,
     *     maxMessage=Errors::MAXIMUN_ODDS
     * )
     * @assert\NotBlank(payload={"level"="selection"}, message=Errors::BAD_STRUCTURE)
     * @Assert\Regex(pattern="/^(\d+(\.\d{1,3})?)$/", payload={"level"="selection"}, message=Errors::BAD_STRUCTURE)
     * @Assert\Type(type="string", payload = {"level"="selection"}, message=Errors::BAD_STRUCTURE)
     */
    private $odds;

    /**
     * Selection constructor.
     *
     * @param int|null   $id
     * @param string|null $odds
     */
    public function __construct($id, $odds)
    {
        $this->id = $id;
        $this->odds = $odds;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getOdds()
    {
        return $this->odds;
    }
}
