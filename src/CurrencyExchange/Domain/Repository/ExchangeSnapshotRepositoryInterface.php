<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Repository;

use App\CurrencyExchange\Domain\Entity\ExchangeSnapshot;
use App\CurrencyExchange\Domain\Entity\ExchangeSymbol;
use App\CurrencyExchange\Infrastructure\DTO\CurrencyExchangeSnapshotDTO;

interface ExchangeSnapshotRepositoryInterface
{
    public function add(ExchangeSnapshot $exchangeSnapshot, bool $flush = false): void;

    public function save(): void;

    public function getLastIntoDTOByCurrencyAssets(
        string $baseAsset, string $quoteAsset
    ): CurrencyExchangeSnapshotDTO;

    public function getLastByCurrencyAssets(ExchangeSymbol $symbol): ExchangeSnapshot;
}
