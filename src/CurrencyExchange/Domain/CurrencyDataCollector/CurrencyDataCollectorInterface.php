<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\CurrencyDataCollector;

use App\CurrencyExchange\Domain\DTO\ExchangeInfoDTO;
use App\CurrencyExchange\Domain\Entity\ExchangeSymbol;

interface CurrencyDataCollectorInterface
{
    public function getAvgPrice(ExchangeSymbol $symbol): float;

    public function exchangeInfo(): ExchangeInfoDTO;
}
