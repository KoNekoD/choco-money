<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\CurrencyAPI;

use App\CurrencyExchange\Domain\CurrencyAPI\DTO\BalanceDTO;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\TransferRequestDTO;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\TransferResponseDTO;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\WalletDTO;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\WalletsListDTO;

interface CurrencyApiEngineInterface
{
    public static function getCurrencyAdapterName(): string;

    public static function getAsset(): string;

    public function createWallet(string $walletName): void;

    public function getWallets(): WalletsListDTO;

    public function getBalance(WalletDTO $wallet): BalanceDTO;

    public function transfer(TransferRequestDTO $transferRequest): TransferResponseDTO;

    public function getWalletByName(string $walletName): WalletDTO;
}
