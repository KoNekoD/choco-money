<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\CurrencyAPI\DTO;

class BalanceDTO
{
    public function __construct(
        public readonly float $balance,
        public readonly float $unlockedBalance,
    )
    {
    }
}
