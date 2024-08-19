<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\CurrencyAPI\Bitcoin\lib;

use App\CurrencyExchange\Domain\CurrencyAPI\DTO\WalletDTO;
use App\CurrencyExchange\Domain\Exception\CurrencyExchangeException;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WalletRPC
{


    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string              $rpcUrl,
        /** @var string[] */
        private readonly array               $basicAuthCredentials,
    )
    {
    }

    /**
     * @return string[] Wallet ids
     * @throws CurrencyExchangeException
     */
    public function listWallets(): array
    {
        return $this->_handleMethod(
            'listwallets'
        );
    }

    /**
     * @phpstan-ignore-next-line
     * @throws CurrencyExchangeException
     */
    private function _handleMethod(
        string $method, ?array $params = null, ?WalletDTO $specifiedWallet = null
    ): mixed
    {
        $params ??= [];
        try {
            // generating unique id per process
            static $requestId = 0;
            $requestId++;

            $json = [
                'method' => $method,
                'params' => $params,
                'id' => $requestId,
            ];

            if ($specifiedWallet) {
                $url = $this->rpcUrl . "/wallet/$specifiedWallet->name";
            } else {
                $url = $this->rpcUrl;
            }

            $response = $this->httpClient->request(
                'POST',
                $url,
                [
                    'json' => $json,
                    'auth_basic' => $this->basicAuthCredentials,
                    'verify_peer' => false,
                    'verify_host' => false
                ]
            );

            $content = $response->getContent(false);

            $data = json_decode($content, true);

            if (isset($data['result'])) {
                return $data['result'];
            }

            throw new CurrencyExchangeException($content);
        } catch (ExceptionInterface $exception) {
            throw new CurrencyExchangeException($exception->getMessage());
        }
    }

    /**
     * @return array{
     *  walletname: string,
     *  walletversion: int,
     *  format: string,
     *  balance: float,
     *  unconfirmed_balance: float,
     *  immature_balance: float,
     *  txcount: int,
     *  keypoololdest: int,
     *  keypoolsize: int,
     *  hdseedid: string,
     *  keypoolsize_hd_internal: int,
     *  paytxfee: float,
     *  private_keys_enabled: bool,
     *  avoid_reuse: bool,
     *  scanning: bool,
     *  descriptors: bool,
     * }
     * @throws CurrencyExchangeException
     */
    public function getWalletInfo(WalletDTO $account): array
    {
        return $this->_handleMethod(
            'getwalletinfo', specifiedWallet: $account
        );
    }

    /** @throws CurrencyExchangeException */
    public function getNewAddress(WalletDTO $account): string
    {
        return $this->_handleMethod(
            'getnewaddress', specifiedWallet: $account
        );
    }

    /**
     * @return array{txid: string}
     * @throws CurrencyExchangeException
     */
    public function sendToAddress(
        string  $address,
        float   $amount,
        ?string $comment = null,
        ?string $commentTo = null,
    ): array
    {
        $params = [$address, (string)$amount];
        if ($comment) {
            $params[] = $comment;
        }
        if ($commentTo) {
            $params[] = $commentTo;
        }

        /** @var array{txid: string} $result */
        $result = $this->_handleMethod(
            'sendtoaddress',
            $params
        );
        return $result;
    }

    /**
     * @return array{name: string, warning: string}
     * @throws CurrencyExchangeException
     */
    public function createWallet(string $walletName): array
    {
        /**
         *
         * Argument #1 - wallet_name Type: string, required
         * The name for the new wallet. If this is a path, the wallet will be created at the path location.
         * Argument #2 - disable_private_keys Type: boolean, optional, default=false
         * Disable the chance of private keys (only watchonlys are possible in this mode).
         * Argument #3 - blank Type: boolean, optional, default=false
         * Create a blank wallet. A blank wallet has no keys or HD seed. One can be set using sethdseed.
         * Argument #4 - passphrase Type: string
         * Encrypt the wallet with this passphrase.
         * Argument #5 - avoid_reuse Type: boolean, optional, default=false
         * Keep track of coin reuse, and treat dirty and clean coins differently with privacy considerations in mind.
         * Argument #6 - descriptors Type: boolean, optional, default=false
         * Create a native descriptor wallet. The wallet will use descriptors internally to handle address creation
         * Argument #7 - load_on_startup Type: boolean, optional, default=null
         * Save wallet name to persistent settings and load on startup. True to add wallet to startup list,
         * false to remove, null to leave unchanged.
         */
        $params = [
            $walletName,
            false,
            false,
            null,
            false,
            false,
            true
        ];
        return $this->_handleMethod(
            'createwallet',
            $params
        );
    }

    /**
     * @throws CurrencyExchangeException
     */
    public function setLabel(string $address, string $label): void
    {
        $this->_handleMethod(
            'setlabel',
            [$address, $label]
        );
    }


    /**
     * @return array{
     *      address : string,
     *      scriptPubKey : string,
     *      ismine : boolean,
     *      iswatchonly : boolean,
     *      solvable : boolean,
     *      desc : string,
     *      isscript : boolean,
     *      ischange : boolean,
     *      iswitness : boolean,
     *      witness_version : int,
     *      witness_program : string,
     *      script : string,
     *      hex : string,
     *      pubkeys : string[],
     *      sigsrequired : int,
     *      pubkey : string,
     *      iscompressed : boolean,
     *      timestamp : int,
     *      hdkeypath : string,
     *      hdseedid : string,
     *      hdmasterfingerprint : string,
     *      labels : string[]
     * }
     * @throws CurrencyExchangeException
     */
    public function getAddressInfo(WalletDTO $account): array
    {
        return $this->_handleMethod(
            'getaddressinfo',
            [$account->address],
            $account
        );
    }
}
