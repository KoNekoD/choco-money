<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\CurrencyAPI\DTO;

class WalletDTO
{
    public function __construct(
        public string $address,
        public string $name,
        public float  $balance,
        public float  $unlockedBalance,
        public int    $index
    )
    {
    }
}
