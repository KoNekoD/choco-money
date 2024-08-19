<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\DTO;

class CreateTransferRequestDTO
{
    public function __construct(
        public readonly string $baseAsset,
        public readonly string $quoteAsset,
        public readonly string $leadBaseWalletAddress,
        public readonly string $leadQuoteWalletAddress,
        public readonly float  $leadBaseExchangeAmount,
        public readonly string $leadEmail
    )
    {
    }
}
