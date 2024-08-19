<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\DTO;

use DateTimeImmutable;
use JsonSerializable;

class CurrencyExchangeSnapshotDTO implements JsonSerializable
{
    public function __construct(
        public readonly DateTimeImmutable $createdAt,
        public readonly string            $baseAsset,
        public readonly string            $quoteAsset,
        public float                      $price
    )
    {
    }

    /** @return array{createdAt: DateTimeImmutable, baseAsset: string, quoteAsset: string, price: float} */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
