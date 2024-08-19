<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Repository;

use App\CurrencyExchange\Domain\DTO\ExchangeInfoSymbolDTO;
use App\CurrencyExchange\Domain\Entity\ExchangeSymbol;
use App\CurrencyExchange\Domain\Exception\CurrencyApiNotFoundException;
use App\CurrencyExchange\Domain\Exception\ExchangeSymbolNotFoundException;
use DateTimeImmutable;

interface ExchangeSymbolRepositoryInterface
{
    public function add(ExchangeSymbol $exchangeSymbol, bool $flush = false): void;

    public function save(): void;

    /**
     * @throws ExchangeSymbolNotFoundException
     */
    public function findOne(string $symbol): ExchangeSymbol;

    public function findOneByAssets(string $currencyOne, string $currencyTwo): ExchangeSymbol;

    /** @return ExchangeSymbol[] */
    public function getSymbolsForSnapshotByDateRange(DateTimeImmutable $from, DateTimeImmutable $to): array;

    /** @throws CurrencyApiNotFoundException */
    public function getExchangeSymbol(ExchangeInfoSymbolDTO $symbol): ExchangeSymbol;
}
