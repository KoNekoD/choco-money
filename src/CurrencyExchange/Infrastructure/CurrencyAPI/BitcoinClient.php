<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\CurrencyAPI;

use App\CurrencyExchange\Domain\CurrencyAPI\CurrencyApiEngineInterface;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\BalanceDTO;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\TransferRequestDTO;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\TransferResponseDTO;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\WalletDTO;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\WalletsListDTO;
use App\CurrencyExchange\Domain\Exception\CurrencyExchangeException;
use App\CurrencyExchange\Infrastructure\CurrencyAPI\Bitcoin\lib\WalletRPC;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BitcoinClient implements CurrencyApiEngineInterface
{
    private WalletRPC $walletRPC;

    /**
     * @param HttpClientInterface $httpClient
     * @param string $walletHost
     * @param string $walletPort
     * @param "https"|"http" $protocol
     * @param ?string $user
     * @param ?string $password
     */
    public function __construct(
        HttpClientInterface $httpClient,
        string              $walletHost,
        string              $walletPort,
        string              $protocol,
        ?string             $user = null,
        ?string             $password = null,
    )
    {
        $rpcUrl = sprintf('%s://%s:%d', $protocol, $walletHost, (int)$walletPort);
        $basicAuthCredentials = [$user, $password];

        $this->walletRPC = new WalletRPC(
            $httpClient,
            $rpcUrl,
            $basicAuthCredentials
        );
    }

    public static function getCurrencyAdapterName(): string
    {
        return 'Bitcoin';
    }

    public static function getAsset(): string
    {
        return 'BTC';
    }

    /** @throws CurrencyExchangeException */
    public function getBalance(WalletDTO $wallet): BalanceDTO
    {
        $info = $this->walletRPC->getWalletInfo($wallet);
        return new BalanceDTO($info['unconfirmed_balance'], $info['balance']);
    }

    /** @throws CurrencyExchangeException */
    public function transfer(TransferRequestDTO $transferRequest): TransferResponseDTO
    {
        $result = $this->walletRPC->sendToAddress(
            $transferRequest->destinationAddress,
            $transferRequest->amount
        );

        return new TransferResponseDTO($result['txid']);
    }

    /** @throws CurrencyExchangeException */
    public function createWallet(string $walletName): void
    {
        $this->walletRPC->createWallet($walletName);
    }

    /** @throws CurrencyExchangeException */
    public function getWalletByName(string $walletName): WalletDTO
    {
        foreach ($this->getWallets()->wallets as $wallet) {
            if ($wallet->name === $walletName) {
                return $wallet;
            }
        }
        throw new CurrencyExchangeException('BitcoinClient::getWalletByName() - Not found wallet');
    }

    /** @throws CurrencyExchangeException */
    public function getWallets(): WalletsListDTO
    {
        $accounts = [];
        $totalBalance = 0;
        $totalUnlockedBalance = 0;

        foreach ($this->walletRPC->listWallets() as $wallet) {
            $accounts[] = new WalletDTO('', $wallet, 0, 0, 0);
        }

        for ($i = 0; $i < count($accounts); $i++) {
            $address = $this->walletRPC->getNewAddress($accounts[$i]);
            $info = $this->walletRPC->getWalletInfo($accounts[$i]);

            $accounts[$i]->address = $address;
            $accounts[$i]->balance = $info['unconfirmed_balance'];
            $accounts[$i]->unlockedBalance = $info['balance'];
            $accounts[$i]->index = $i;

            $totalBalance += $info['unconfirmed_balance'];
            $totalUnlockedBalance += $info['balance'];
        }

        return new WalletsListDTO($accounts, $totalBalance, $totalUnlockedBalance);
    }
}
