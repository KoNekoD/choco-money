<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Repository;

use App\CurrencyExchange\Domain\Entity\DelayedTransfer;
use App\CurrencyExchange\Domain\Exception\DelayedTransferNotFoundException;

interface DelayedTransferRepositoryInterface
{
    public function add(DelayedTransfer $delayedTransfer, bool $flush = false): void;

    public function save(): void;

    /**
     * @throws DelayedTransferNotFoundException
     */
    public function findOne(string $id): DelayedTransfer;

    /** @return DelayedTransfer[] */
    public function findPendingTransfers(): array;

    /**
     * @param string[] $names
     * @return DelayedTransfer[]
     */
    public function findPendingTransfersByExchangerBaseWalletNames(array $names): array;

    /** @return DelayedTransfer[] */
    public function findMutualMoneySentTransfers(): array;
}
