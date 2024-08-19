<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\DTO;

use App\CurrencyExchange\Domain\CurrencyAPI\CurrencyApiEngineInterface;
use JsonSerializable;

class CurrencyDTO implements JsonSerializable
{
    public function __construct(
        public string $name,
        public string $asset
    )
    {
    }

    /**
     * @param CurrencyApiEngineInterface[] $apis
     * @return self[]
     */
    public static function fromApisArray(array $apis): array
    {
        $result = [];
        foreach ($apis as $api) {
            $result[] = new self(
                $api::getCurrencyAdapterName(),
                $api::getAsset()
            );
        }
        return $result;
    }

    /** @return array{asset: string, name: string} */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
