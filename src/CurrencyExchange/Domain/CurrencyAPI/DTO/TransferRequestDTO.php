<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\CurrencyAPI\DTO;

class TransferRequestDTO
{
    public function __construct(
        public readonly WalletDTO $fromWallet,
        public readonly string    $destinationAddress,
        public readonly float     $amount
    )
    {
    }
}
