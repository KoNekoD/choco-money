<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Provider;

use App\CurrencyExchange\Domain\CurrencyAPI\CurrencyApiEngineInterface;
use App\CurrencyExchange\Domain\Exception\CurrencyApiNotFoundException;
use Traversable;

class CurrencyApiProvider
{
    /** @var array<string, CurrencyApiEngineInterface> $apis */
    private array $apis;

    /** @param iterable<CurrencyApiEngineInterface> $apis */
    public function __construct(
//        #[TaggedIterator('choco.currency_api', defaultIndexMethod: 'getCurrencyAdapterName')]
        iterable $apis
    )
    {
        $this->apis = $apis instanceof Traversable ? iterator_to_array($apis) : $apis;
    }

    public function getApi(string $api): CurrencyApiEngineInterface
    {
        return $this->apis[$api];
    }

    /** @throws CurrencyApiNotFoundException */
    public function getApiByAsset(string $asset): CurrencyApiEngineInterface
    {
        foreach ($this->apis as $api) {
            if ($api::getAsset() === $asset) {
                return $api;
            }
        }
        throw new CurrencyApiNotFoundException();
    }

    public function isValid(string $api): bool
    {
        return in_array($api, $this->apis);
    }

    /** @return string[] */
    public function getNamesList(): array
    {
        return array_keys($this->apis);
    }

    /** @return CurrencyApiEngineInterface[] */
    public function getList(): array
    {
        return array_values($this->apis);
    }
}
