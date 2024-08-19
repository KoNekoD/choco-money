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
use App\CurrencyExchange\Infrastructure\CurrencyAPI\Monero\lib\WalletRPC;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MoneroClient implements CurrencyApiEngineInterface
{
    private readonly WalletRPC $walletRPC;

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
        $protocol = 'https';
        $this->walletRPC = new walletRPC(
            $walletHost, (int)$walletPort, $protocol, $user, $password, $httpClient
        );
    }

    public static function getCurrencyAdapterName(): string
    {
        return 'Monero';
    }

    public static function getAsset(): string
    {
        return 'XMR';
    }

    public function getBalance(WalletDTO $wallet): BalanceDTO
    {
        $response = $this->walletRPC->getBalance($wallet->index);

        return new BalanceDTO(
            (float)$response['balance'],
            (float)$response['unlocked_balance']
        );
    }

    public function transfer(TransferRequestDTO $transferRequest): TransferResponseDTO
    {
        $result = $this->walletRPC->transfer(
            $transferRequest->amount,
            $transferRequest->destinationAddress,
            $transferRequest->fromWallet->index,
        );
        return new TransferResponseDTO($result['tx_key']);
    }

    public function createWallet(string $walletName): void
    {
        $this->walletRPC->createAccount($walletName);
    }

    /** @throws CurrencyExchangeException */
    public function getWalletByName(string $walletName): WalletDTO
    {
        foreach ($this->getWallets()->wallets as $wallet) {
            if ($wallet->name === $walletName) {
                return $wallet;
            }
        }
        throw new CurrencyExchangeException('MoneroClient::getWalletByName() - Not found wallet');
    }

    public function getWallets(): WalletsListDTO
    {
        $response = $this->walletRPC->getAccounts();

        $accounts = [];
        foreach ($response['subaddress_accounts'] as $item) {
            $accounts[] = new WalletDTO(
                $item['base_address'],
                $item['label'],
                (float)$item['balance'],
                (float)$item['unlocked_balance'],
                $item['account_index']
            );
        }

        return new WalletsListDTO(
            $accounts,
            $response['total_balance'],
            $response['total_unlocked_balance']
        );
    }
}
