<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Entity;

use App\CurrencyExchange\Domain\CurrencyAPI\CurrencyApiEngineInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class ExchangeSymbol
{
    #[ORM\Column]
    private readonly string $baseName;

    #[ORM\Column]
    private readonly string $baseAsset;

    #[ORM\Column]
    private readonly string $quoteName;

    #[ORM\Column]
    private readonly string $quoteAsset;


    public function __construct(
        #[ORM\Id, ORM\GeneratedValue(strategy: 'NONE'), ORM\Column]
        private readonly string    $symbol,
        CurrencyApiEngineInterface $baseAsset,
        CurrencyApiEngineInterface $quoteAsset,
    )
    {
        $this->baseName = $baseAsset::getCurrencyAdapterName();
        $this->baseAsset = $baseAsset::getAsset();

        $this->quoteName = $quoteAsset::getCurrencyAdapterName();
        $this->quoteAsset = $quoteAsset::getAsset();
    }

    public function getSymbol(): string
    {
        return $this->symbol;
    }

    public function getBaseName(): string
    {
        return $this->baseName;
    }

    public function getBaseAsset(): string
    {
        return $this->baseAsset;
    }

    public function getQuoteName(): string
    {
        return $this->quoteName;
    }

    public function getQuoteAsset(): string
    {
        return $this->quoteAsset;
    }
}
