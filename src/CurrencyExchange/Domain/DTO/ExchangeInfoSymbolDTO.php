<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\DTO;

class ExchangeInfoSymbolDTO
{
    public function __construct(
        public readonly string $symbol,
        public readonly string $baseAsset,
        public readonly string $quoteAsset
    )
    {
    }
}
