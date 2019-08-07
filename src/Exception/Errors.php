<?php declare(strict_types = 1);

namespace App\Exception;

class Errors
{
    public const UNKNOWN_ERROR = 0;
    public const BAD_STRUCTURE = 1;
    public const MINIMUM_STAKE_AMOUNT = 2;
    public const MAXIMUM_STAKE_AMOUNT = 3;
    public const MINIMUM_SELECTIONS_NUMBER = 4;
    public const MAXIMUM_SELECTIONS_NUMBER = 5;
    public const MINIMUM_ODDS = 6;
    public const MAXIMUN_ODDS = 7;
    public const DUPLICATE_SELECTION = 8;
    public const MAX_WIN_AMOUNT = 9;
    public const NOT_FINISHED_ACTION = 10;
    public const INSUFFICIENT_BALANCE = 11;

    public static $errorMessages = [
        0 => 'Unknown error',
        1 => 'Betslip structure mismatch',
        2 => 'Minimum stake amount is {{ limit }}',
        3 => 'Maximum stake amount is {{ limit }}',
        4 => 'Minimum number of selections is {{ limit }}',
        5 => 'Maximum number of selections is {{ limit }}',
        6 => 'Minimum odds are {{ limit }}',
        7 => 'Maximum odds are {{ limit }}',
        8 => 'Duplicate selection found',
        9 => 'Maximum win amount is {{ limit }}',
        10 => 'Your previous action is not finished yet',
        11 => 'Insufficient balance',
    ];
}
