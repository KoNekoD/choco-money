<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\DTO;

use App\CurrencyExchange\Domain\Entity\DelayedTransfer;
use JsonSerializable;

class TransferDTO implements JsonSerializable
{
    public function __construct(
        public readonly string $id,
        public readonly string $baseAsset,
        public readonly string $quoteAsset,
        public readonly string $expiresAt,
        public readonly float  $leadBaseExchangeAmount,
        // Exchanger wallet address for receive lead's money
        public readonly string $exchangerBaseWalletAddress,
    )
    {
    }

    public static function fromEntity(DelayedTransfer $transfer): self
    {
        return new self(
            $transfer->getId(),
            $transfer->getBaseAsset(),
            $transfer->getQuoteAsset(),
            $transfer->getCreatedAt()->modify(
                sprintf(
                    '+%d minutes',
                    DelayedTransfer::MAX_WAIT_MINUTES
                )
            )->format('Y-m-d H:i:s'),
            $transfer->getLeadBaseExchangeAmount(),
            $transfer->getExchangerBaseWalletAddress()
        );
    }

    /** @return array{
     *      baseAsset: string,
     *      quoteAsset: string,
     *      expiresAt: string,
     *      leadBaseExchangeAmount: float,
     *      exchangerBaseWalletAddress: string,
     * }
     */
    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}
