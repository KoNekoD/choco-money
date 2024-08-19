<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\DTO;

class CancelTransferRequestDTO
{
    public function __construct(public readonly string $reason)
    {
    }
}
