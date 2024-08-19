<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\CurrencyAPI\DTO;

class WalletsListDTO
{
    /** @param array<WalletDTO> $wallets */
    public function __construct(
        public array          $wallets,
        public readonly float $totalBalance,
        public readonly float $totalUnlockedBalance
    )
    {
    }

    /** @return string[] */
    public function toWalletsNamesArray(): array
    {
        $addresses = [];
        foreach ($this->wallets as $wallet) {
            $addresses[] = $wallet->name;
        }
        return $addresses;
    }
}
