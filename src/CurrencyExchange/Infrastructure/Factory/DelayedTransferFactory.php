<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Factory;

use App\CurrencyExchange\Domain\Entity\DelayedTransfer;
use App\CurrencyExchange\Domain\Exception\CurrencyExchangeException;
use App\CurrencyExchange\Domain\Repository\DelayedTransferRepositoryInterface;
use App\CurrencyExchange\Infrastructure\DTO\CreateTransferRequestDTO;
use App\CurrencyExchange\Infrastructure\Provider\CurrencyApiProvider;

class DelayedTransferFactory
{
    public function __construct(
        private readonly
        DelayedTransferRepositoryInterface $delayedTransferRepository,
        private readonly
        CurrencyApiProvider                $currencyApiProvider,
    )
    {
    }

    /** @throws CurrencyExchangeException */
    public function create(CreateTransferRequestDTO $DTO): DelayedTransfer
    {
        $baseEngine = $this->currencyApiProvider->getApiByAsset(
            $DTO->baseAsset
        );
        $quoteEngine = $this->currencyApiProvider->getApiByAsset(
            $DTO->quoteAsset
        );

        $baseWalletsDTO = $baseEngine->getWallets();
        $walletsNames = $baseWalletsDTO->toWalletsNamesArray();

        $pendingTransfersByOwnAddresses = $this
            ->delayedTransferRepository
            ->findPendingTransfersByExchangerBaseWalletNames(
                $walletsNames
            );

        foreach ($pendingTransfersByOwnAddresses as $transfer) {
            $index = array_search(
                $transfer->getExchangerBaseWalletAddress(),
                $walletsNames
            );
            if ($index) {
                unset($walletsNames[$index]);
            }
        }

        if (empty($walletsNames)) {
            throw new CurrencyExchangeException(
                'Not found available exchanger wallet'
            );
        }

        $exchangerBaseWalletName = $walletsNames[0];

        $selectedWallet = null;

        $exchangerBaseWalletBalanceAmountBeforeReceiveMoney = null;
        foreach ($baseWalletsDTO->wallets as $wallet) {
            if ($wallet->name === $exchangerBaseWalletName) {
                $selectedWallet = $wallet;
                $exchangerBaseWalletBalanceAmountBeforeReceiveMoney = $wallet->balance;
            }
        }

        if (null === $exchangerBaseWalletBalanceAmountBeforeReceiveMoney) {
            throw new \LogicException();
        }

        return new DelayedTransfer(
            $selectedWallet->address,
            $exchangerBaseWalletBalanceAmountBeforeReceiveMoney,
            $exchangerBaseWalletName,
            $DTO->leadBaseWalletAddress,
            $DTO->leadQuoteWalletAddress,
            $DTO->leadBaseExchangeAmount,
            $DTO->leadEmail,
            $baseEngine,
            $quoteEngine
        );
    }
}
