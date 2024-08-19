<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Service;

use App\CurrencyExchange\Domain\Exception\CurrencyExchangeException;
use App\CurrencyExchange\Domain\Repository\ExchangeSnapshotRepositoryInterface;
use App\CurrencyExchange\Infrastructure\DTO\CurrencyExchangeSnapshotDTO;

class ExchangeCalculationService
{
    const COMMISSION_MULTIPLIER = 1.01;

    public function __construct(private readonly ExchangeSnapshotRepositoryInterface $snapshotRepository)
    {
    }

    /** @throws CurrencyExchangeException */
    public function calculateCurrencyExchange(string $baseAsset, string $quoteAsset): CurrencyExchangeSnapshotDTO
    {
        if ($baseAsset === $quoteAsset) {
            throw new CurrencyExchangeException('Using baseAsset and quoteAsset is not allowed now');
        }

        $exchangeRate = $this->snapshotRepository->getLastIntoDTOByCurrencyAssets(
            $baseAsset,
            $quoteAsset
        );

        $exchangeRate->price = round(($exchangeRate->price * self::COMMISSION_MULTIPLIER), 6);

        return $exchangeRate;
    }

    public function calculateCurrencyExchangePrice(CurrencyExchangeSnapshotDTO $snapshot, float $baseAmount): float
    {
        $snapshot->price = $snapshot->price * self::COMMISSION_MULTIPLIER;

        return $snapshot->price * $baseAmount;
    }
}
