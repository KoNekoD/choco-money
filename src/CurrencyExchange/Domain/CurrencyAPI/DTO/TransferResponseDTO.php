<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\CurrencyAPI\DTO;

class TransferResponseDTO
{
    public function __construct(public readonly string $transactionKey)
    {
    }
}
