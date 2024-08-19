<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Entity;

use App\CurrencyExchange\Infrastructure\DTO\CurrencyExchangeSnapshotDTO;
use App\Shared\Domain\Service\UlidService;
use Carbon\CarbonImmutable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ExchangeSnapshot
{
    public const UPDATE_PERIOD = '1 minute';

    #[ORM\Id, ORM\GeneratedValue(strategy: 'NONE'), ORM\Column]
    private readonly string $id;

    #[ORM\Column]
    private DateTimeImmutable $createdAt;

    public function __construct(
        #[ORM\ManyToOne, ORM\JoinColumn(referencedColumnName: 'symbol', nullable: false)]
        private readonly ExchangeSymbol $symbol,
        #[ORM\Column]
        private readonly float          $price,
    )
    {
        $this->id = UlidService::generate();
        $this->createdAt = new CarbonImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getSymbol(): ExchangeSymbol
    {
        return $this->symbol;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function toDTO(string $baseAsset): CurrencyExchangeSnapshotDTO
    {
        if ($this->symbol->getBaseAsset() === $baseAsset) {
            return new CurrencyExchangeSnapshotDTO(
                createdAt: $this->createdAt,
                baseAsset: $this->symbol->getBaseAsset(),
                quoteAsset: $this->symbol->getQuoteAsset(),
                price: $this->getPrice()
            );
        }
        return new CurrencyExchangeSnapshotDTO(
            createdAt: $this->createdAt,
            baseAsset: $this->symbol->getQuoteAsset(),
            quoteAsset: $this->symbol->getBaseAsset(),
            price: (1 / $this->getPrice())
        );
    }
}
