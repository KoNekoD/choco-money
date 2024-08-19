<?php

namespace App\CurrencyExchange\Infrastructure\CurrencyAPI\Monero\lib;

use Exception;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class WalletRPC
{
    public const TRANSFER_PRIORITY = [
        'unimportant' => 0,
        'normal' => 1,
        'elevated' => 2,
        'priority' => 3,
    ];
    private JsonRPCClient $client;
    private string $url;
    private ?string $user;
    private ?string $password;

    /**
     * Start a connection with the Monero wallet RPC interface (monero-wallet-rpc)
     *
     * @param string $host monero-wallet-rpc hostname               (optional)
     * @param int $port monero-wallet-rpc port                   (optional)
     * @param string $protocol monero-wallet-rpc protocol (eg. 'http')  (optional)
     * @param ?string $user monero-wallet-rpc username               (optional)
     * @param ?string $password monero-wallet-rpc passphrase             (optional)
     *
     */
    function __construct(
        string              $host,
        int                 $port,
        string              $protocol,
        ?string             $user,
        ?string             $password,
        HttpClientInterface $httpClient
    )
    {
        $this->user = $user;
        $this->password = $password;

        $this->url = $protocol . '://' . $host . ':' . $port . '/json_rpc';
        $this->client = new JsonRPCClient($this->url, $this->user, $this->password, $httpClient);
    }

    /**
     * Look up an account's balance
     *
     * @param int $account_index Index of account to look up
     *
     * @return array{balance: float, unlocked_balance: float}
     */
    public function getBalance(int $account_index): array
    {
        return $this->_run('get_balance', [
            'account_index' => $account_index
        ]);
    }

    /**
     * Execute command via JsonRPCClient
     *
     * @param string $method RPC method to call
     * @param ?mixed $params Parameters to pass  (optional)
     *
     * @return mixed  Call result
     */
    private function _run(string $method, mixed $params = null): mixed
    {
        return $this->client->_run($method, $params);
    }

    /**
     * Look up wallet address(es)
     *
     * @param int $accountIndex Index of account to look up
     * @param int $subAddressIndex Index of subAddress to look up  (optional)
     *
     * @return array{
     *     address: string,
     *     addresses: array{
     *          address: string,
     *          address_index: int,
     *          label: string,
     *          used: bool
     *      }[]
     * }
     */
    public function getAddress(int $accountIndex, int $subAddressIndex = 0): array
    {
        return $this->_run('get_address', [
            'account_index' => $accountIndex, 'address_index' => $subAddressIndex
        ]);
    }

    /**
     * Create a new subAddress
     *
     * @param int $account_index The subAddress account index
     * @param string $label A label to apply to the new subAddress
     *
     * @return array{address: string, address_index: int}
     */
    public function createSubAddress(int $account_index, string $label): array
    {
        $create_address_method = $this->_run('create_address', [
            'account_index' => $account_index, 'label' => $label
        ]);

        $this->store(); // Save wallet state after subAddress creation

        return $create_address_method;
    }

    /**
     * Save wallet
     */
    public function store(): void
    {
        $this->_run('store');
    }

    /**
     * Label a subAddress
     *
     * @param int $index The index of the subAddress to label
     * @param string $label The label to apply
     */
    public function labelSubAddress(int $index, string $label): void
    {
        $this->_run('label_address', [
            'index' => $index, 'label' => $label
        ]);
    }

    /**
     * Look up wallet accounts
     *
     * @return array{
     *     subaddress_accounts: array{
     *          account_index: int,
     *          balance: float,
     *          base_address: string,
     *          label: string,
     *          tag: string,
     *          unlocked_balance: float
     *      }[],
     *     total_balance: float,
     *     total_unlocked_balance: float
     * }
     */
    public function getAccounts(): array
    {
        return $this->_run('get_accounts');
    }

    /**
     * Create a new account
     *
     * @param string $label Label to apply to new account
     */
    public function createAccount(string $label): void
    {
        $this->_run('create_account', [
            'label' => $label
        ]);

        $this->store(); // Save wallet state after account creation
    }

    /**
     * Label an account
     *
     * @param int $account_index Index of account to label
     * @param string $label Label to apply
     */
    public function labelAccount(int $account_index, string $label): void
    {
        $this->_run('label_account', [
            'account_index' => $account_index, 'label' => $label
        ]);

        $this->store(); // Save wallet state after account label
    }

    /**
     * @FIXME Returnable result in doubt
     * Look up account tags
     *
     * @return array  Example: {
     *   "account_tags": {
     *     "0": {
     *       "accounts": {
     *         "0": 0,
     *         "1": 1
     *       },
     *       "label": "",
     *       "tag": "Example tag"
     *     }
     *   }
     * }
     */
    public function getAccountTags(): array
    {
        return $this->_run('get_account_tags');
    }

    /**
     * Tag accounts
     *
     * @param int[] $accounts The indices of the accounts to tag
     * @param string $tag Tag to apply
     */
    public function tagAccounts(array $accounts, string $tag): void
    {
        $this->_run('tag_accounts', [
            'accounts' => $accounts, 'tag' => $tag
        ]);

        $this->store(); // Save wallet state after account tagging
    }

    /**
     * Untag accounts
     *
     * @param int[] $accounts The indices of the accounts to untag
     */
    public function untag_accounts(array $accounts): void
    {
        $this->_run('untag_accounts', [
            'accounts' => $accounts
        ]);

        $this->store(); // Save wallet state after untagging accounts
    }

    /**
     * Describe a tag
     *
     * @param string $tag Tag to describe
     * @param string $description Description to apply to tag
     */
    public function setAccountTagDescription(string $tag, string $description): void
    {
        $this->_run('set_account_tag_description', [
            'tag' => $tag, 'description' => $description
        ]);

        $this->store(); // Save wallet state after describing tag
    }

    /**
     * Look up how many blocks are in the longest chain known to the wallet
     *
     * @return array{height: int}
     */
    public function getHeight(): array
    {
        return $this->_run('get_height');
    }

    /**
     * Send monero
     * Parameters can be passed in individually (as listed below) or as an array/dictionary (as listed at bottom)
     * To send to multiple recipients, use the array/dictionary (bottom) format and pass an array of recipient addresses and amount arrays in the destinations field (as in "destinations = [['amount' => 1, 'address' => ...], ['amount' => 2, 'address' => ...]]")
     *
     * @param float $amount Amount of monero to send
     * @param string $address Address to receive funds
     * @param ?string $payment_id Payment ID                                                (optional)
     * @param ?int $mixin Mixin number (ringsize - 1)                               (optional)
     * @param int $account_index Account to send from                                      (optional)
     * @param ?string $subaddr_indices Comma-separeted list of subaddress indices to spend from  (optional)
     * @param ?value-of<WalletRPC::TRANSFER_PRIORITY> $priority Transaction priority. 0-3 for: default, unimportant, normal, elevated, priority. (optional)// TODO Подсказки
     * @param ?int $unlock_time UNIX time or block height to unlock output                (optional)
     * @param ?boolean $do_not_relay Do not relay transaction                                  (optional)
     *
     * @return array{amount: float, fee: float, tx_hash: string, tx_key: string}  Example: {
     *   "amount": "1000000000000",
     *   "fee": "1000020000",
     *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
     *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
     * }
     *
     */
    public function transfer(
        float   $amount,
        string  $address,
        int     $account_index,
        ?string $payment_id = null,
        ?int    $mixin = null,
        ?string $subaddr_indices = null,
        ?int    $priority = null,
        ?int    $unlock_time = null,
        ?bool   $do_not_relay = null
    ): array
    {
        $destinations = [
            [
                'amount' => $this->_transform($amount),
                'address' => $address
            ]
        ];

        $transfer_method = $this->_run('transfer', [
            'destinations' => $destinations,
            'mixin' => $mixin ?? 6, // Default 6
            'get_tx_key' => true,
            'payment_id' => $payment_id ?? '', // Default empty
            'account_index' => $account_index,
            'subaddr_indices' => $subaddr_indices ?? '', // Default empty
            'priority' => $priority ?? 0, // Default low priority
            'unlock_time' => $unlock_time ?? 0, // Default 0
            'do_not_relay' => $do_not_relay ?? false // Default false
        ]);

        $this->store(); // Save wallet state after transfer

        return $transfer_method;
    }

    /**
     * Convert from moneroj to tacoshi (piconero)
     *
     * @param float $amount
     * @return float|int
     */
    public function _transform(float $amount = 0): float|int
    {
        return $amount * 1000000000000;
    }

    /**
     * Same as transfer, but splits transfer into more than one transaction if necessary
     *
     * @param array{amount: float, address: string}[] $destinations
     * @param int $accountIndex
     * @param ?string $paymentId
     * @param ?positive-int $ringSize Sets ringsize to n (mixin + 1). (Unless dealing with pre rct outputs, this field is ignored on mainnet).
     * @param ?string $subAddrIndices
     * @param ?value-of<WalletRPC::TRANSFER_PRIORITY> $priority
     * @param ?int $unlock_time
     * @param bool $do_not_relay
     *
     * @return mixed
     *
     */
    public function transfer_split(
        array   $destinations,
        int     $accountIndex,
        ?string $paymentId = null,
        ?int    $ringSize = null,
        ?string $subAddrIndices = null,
        ?int    $priority = null,
        ?int    $unlock_time = null,
        ?bool   $do_not_relay = null
    ): mixed
    {
        for ($i = 0; $i < count($destinations); $i++) {
            $destinations[$i]['amount'] = $this->_transform(
                $destinations[$i]['amount']
            );
        }

        $transfer_method = $this->_run('transfer_split', [
            'destinations' => $destinations,
            'ring_size' => $ringSize ?? 6 + 1, // Default mixin=6
            'get_tx_key' => true,
            'account_index' => $accountIndex,
            'subaddr_indices' => $subAddrIndices ?? '', // Default empty
            'payment_id' => $paymentId ?? '', // Default empty
            'priority' => $priority ?? 0, // Default low priority
            'unlock_time' => $unlock_time ?? 0, // Default 0
            'do_not_relay' => $do_not_relay ?? false // Default false
        ]);

        $this->store(); // Save wallet state after transfer

        return $transfer_method;
    }

    /**
     * Send all dust outputs back to the wallet
     *
     * @return array  Example: {
     *   // TODO example
     * }
     */
    public function sweep_dust(): array
    {
        return $this->_run('sweep_dust');
    }

    /**
     * Send all unmixable outputs back to the wallet
     *
     *
     * @return array  Example: {
     *   // TODO example
     * }
     */
    public function sweep_unmixable(): array
    {
        return $this->_run('sweep_unmixable');
    }

    /**
     * Send all unlocked outputs from an account to an address
     *
     * @param string $address Address to receive funds
     * @param int $account_index Index of the account to sweep                        (optional)
     * @param ?string $subaddr_indices Comma-seperated list of subaddress indices to sweep  (optional)
     * @param ?string $payment_id Payment ID                                           (optional)
     * @param ?int $mixin Mixin number (ringsize - 1)                          (optional)
     * @param ?int $priority Payment ID                                           (optional)
     * @param ?int $below_amount Only send outputs below this amount                  (optional)
     * @param ?int $unlock_time UNIX time or block height to unlock output           (optional)
     * @param boolean $do_not_relay Do not relay transaction                             (optional)
     *
     * @return array{amount: float, fee: float, tx_hash: string, tx_key: string}  Example: {
     *   "amount": "1000000000000",
     *   "fee": "1000020000",
     *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
     *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
     * }
     */
    public function sweep_all(
        string  $address,
        int     $account_index,
        ?string $subaddr_indices = null,
        ?string $payment_id = null,
        ?int    $mixin = null,
        ?int    $priority = null,
        ?int    $below_amount = null,
        ?int    $unlock_time = null,
        bool    $do_not_relay = false
    ): array
    {
        $sweep_all_method = $this->_run('sweep_all', [
            'address' => $address,
            'mixin' => $mixin ?? 6,
            'get_tx_key' => true,
            'subaddr_indices' => $subaddr_indices ?? '',
            'account_index' => $account_index,
            'payment_id' => $payment_id ?? '',
            'priority' => $priority ?? 0,
            'below_amount' => $this->_transform($below_amount ?? 0),
            'unlock_time' => $unlock_time ?? 0,
            'do_not_relay' => $do_not_relay
        ]);

        $this->store(); // Save wallet state after transfer

        return $sweep_all_method;
    }

    /**
     *
     * Sweep a single key image to an address
     *
     * @param string $key_image Key image to sweep
     * @param string $address Address to receive funds
     * @param string $payment_id Payment ID                                  (optional)
     * @param int $mixin Mixin number (ringsize - 1)                 (optional)
     * @param int $priority Payment ID                                  (optional)
     * @param int $below_amount Only send outputs below this amount         (optional)
     * @param int $unlock_time UNIX time or block height to unlock output  (optional)
     * @param int $do_not_relay Do not relay transaction                    (optional)
     *
     *   OR
     *
     * @return array  Example: {
     *   "amount": "1000000000000",
     *   "fee": "1000020000",
     *   "tx_hash": "c60a64ddae46154a75af65544f73a7064911289a7760be8fb5390cb57c06f2db",
     *   "tx_key": "805abdb3882d9440b6c80490c2d6b95a79dbc6d1b05e514131a91768e8040b04"
     * }
     *
     * @throws Exception
     */
    public function sweep_single(
        string $key_image,
        string $address,
        string $payment_id = '',
        int    $mixin = 6,
        int    $priority = 2,
        int    $below_amount = 0,
        int    $unlock_time = 0,
        int    $do_not_relay = 0
    ): array
    {
        if (is_array($key_image)) { // Parameters passed in as array/dictionary
            $params = $key_image;

            if (array_key_exists('key_image', $params)) {
                $key_image = $params['key_image'];
            } else {
                throw new Exception('Error: Key image required');
            }
            if (array_key_exists('address', $params)) {
                $address = $params['address'];
            } else {
                throw new Exception('Error: Address required');
            }

            if (array_key_exists('payment_id', $params)) {
                $payment_id = $params['payment_id'];
            }
            if (array_key_exists('mixin', $params)) {
                $mixin = $params['mixin'];
            }
            if (array_key_exists('account_index', $params)) {
                $account_index = $params['account_index'];
            }
            if (array_key_exists('priority', $params)) {
                $priority = $params['priority'];
            }
            if (array_key_exists('unlock_time', $params)) {
                $unlock_time = $params['unlock_time'];
            }
            if (array_key_exists('unlock_time', $params)) {
                $unlock_time = $params['unlock_time'];
            }
            if (array_key_exists('below_amount', $params)) {
                $below_amount = $params['below_amount'];
            }
            if (array_key_exists('do_not_relay', $params)) {
                $do_not_relay = $params['do_not_relay'];
            }
        }

        $params = array('address' => $address, 'mixin' => $mixin, 'get_tx_key' => true, 'account_index' => $account_index, 'payment_id' => $payment_id, 'priority' => $priority, 'below_amount' => $this->_transform($below_amount), 'unlock_time' => $unlock_time, 'do_not_relay' => $do_not_relay);
        $sweep_single_method = $this->_run('sweep_single', $params);

        $this->store(); // Save wallet state after transfer

        return $sweep_single_method;
    }

    /**
     *
     * Relay a transaction
     *
     * @param string $hex Blob of transaction to relay
     *
     * @return array  // TODO example
     *
     */
    public function relay_tx(string $hex): array
    {
        $params = array('hex' => $hex);
        $relay_tx_method = $this->_run('relay_tx_method', $params);

        $this->store(); // Save wallet state after transaction relay

        return $this->_run('relay_tx');
    }

    /**
     *
     * Look up incoming payments by payment ID
     *
     * @param string $payment_id Payment ID to look up
     *
     * @return array  Example: {
     *   "payments": [{
     *     "amount": 10350000000000,
     *     "block_height": 994327,
     *     "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "unlock_time": 0
     *   }]
     * }
     *
     */
    public function get_payments(string $payment_id): array
    {
        // $params = array('payment_id' => $payment_id); // does not work
        $params = [];
        $params['payment_id'] = $payment_id;
        return $this->_run('get_payments', $params);
    }

    /**
     *
     * Look up incoming payments by payment ID (or a list of payments IDs) from a given height
     *
     * @param array $payment_ids Array of payment IDs to look up
     * @param string $min_block_height Height to begin search
     *
     * @return array  Example: {
     *   "payments": [{
     *     "amount": 10350000000000,
     *     "block_height": 994327,
     *     "payment_id": "4279257e0a20608e25dba8744949c9e1caff4fcdafc7d5362ecf14225f3d9030",
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "unlock_time": 0
     *   }]
     * }
     *
     * @throws Exception
     */
    public function get_bulk_payments(array $payment_ids, string $min_block_height): array
    {
        // $params = array('payment_ids' => $payment_ids, 'min_block_height' => $min_block_height); // does not work
        $params = array('min_block_height' => $min_block_height); // does not work
        $params = [];
        if (!is_array($payment_ids)) {
            throw new Exception('Error: Payment IDs must be array.');
        }
        if ($payment_ids) {
            $params['payment_ids'] = [];
            foreach ($payment_ids as $payment_id) {
                $params['payment_ids'][] = $payment_id;
            }
        }
        return $this->_run('get_bulk_payments', $params);
    }

    /**
     *
     * Look up incoming transfers
     *
     * @param string $type Type of transfer to look up; must be 'all', 'available', or 'unavailable' (incoming transfers which have already been spent)
     * @param int $account_index Index of account to look up                                                                                                   (optional)
     * @param string $subaddr_indices Comma-seperated list of subaddress indices to look up                                                                         (optional)
     *
     * @return array  Example: {
     *   "transfers": [{
     *     "amount": 10000000000000,
     *     "global_index": 711506,
     *     "spent": false,
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "tx_size": 5870
     *   },{
     *     "amount": 300000000000,
     *     "global_index": 794232,
     *     "spent": false,
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "tx_size": 5870
     *   },{
     *     "amount": 50000000000,
     *     "global_index": 213659,
     *     "spent": false,
     *     "tx_hash": "c391089f5b1b02067acc15294e3629a463412af1f1ed0f354113dd4467e4f6c1",
     *     "tx_size": 5870
     *   }]
     * }
     */
    public function incoming_transfers(string $type = 'all', int $account_index = 0, string $subaddr_indices = ''): array
    {
        $params = array('transfer_type' => $type, 'account_index' => $account_index, 'subaddr_indices' => $subaddr_indices);
        return $this->_run('incoming_transfers', $params);
    }

    /**
     *
     * Look up a wallet key
     *
     * @param string $key_type Type of key to look up; must be 'view_key', 'spend_key', or 'mnemonic'
     *
     * @return array  Example: {
     *   "key": "7e341d..."
     * }
     *
     */
    public function query_key(string $key_type): array
    {
        $params = array('key_type' => $key_type);
        return $this->_run('query_key', $params);
    }

    /**
     *
     * Look up wallet view key
     *
     *
     * @return array  Example: {
     *   "key": "7e341d..."
     * }
     *
     */
    public function view_key(): array
    {
        $params = array('key_type' => 'view_key');
        return $this->_run('query_key', $params);
    }

    /**
     *
     * Look up wallet spend key
     *
     *
     * @return array  Example: {
     *   "key": "2ab810..."
     * }
     *
     */
    public function spend_key(): array
    {
        $params = array('key_type' => 'spend_key');
        return $this->_run('query_key', $params);
    }

    /**
     *
     * Look up wallet mnemonic seed
     *
     *
     * @return array  Example: {
     *   "key": "2ab810..."
     * }
     *
     */
    public function mnemonic(): array
    {
        $params = array('key_type' => 'mnemonic');
        return $this->_run('query_key', $params);
    }

    /**
     *
     * Create an integrated address from a given payment ID
     *
     * @param string|null $payment_id Payment ID  (optional)
     *
     * @return array  Example: {
     *   "integrated_address": "4BpEv3WrufwXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQQ8H2RRJveAtUeiFs6J"
     * }
     *
     */
    public function make_integrated_address(string $payment_id = null): array
    {
        $params = array('payment_id' => $payment_id);
        return $this->_run('make_integrated_address', $params);
    }

    /**
     *
     * Look up the wallet address and payment ID corresponding to an integrated address
     *
     * @param string $integrated_address Integrated address to split
     *
     * @return array  Example: {
     *   "payment_id": "420fa29b2d9a49f5",
     *   "standard_address": "427ZuEhNJQRXoyJAeEoBaNW56ScQaLXyyQWgxeRL9KgAUhVzkvfiELZV7fCPBuuB2CGuJiWFQjhnhhwiH1FsHYGQGaDsaBA"
     * }
     *
     */
    public function split_integrated_address(string $integrated_address): array
    {
        $params = array('integrated_address' => $integrated_address);
        return $this->_run('split_integrated_address', $params);
    }

    /**
     *
     * Stop the wallet, saving the state
     *
     *
     *
     */
    public function stop_wallet()
    {
        return $this->_run('stop_wallet');
    }

    /**
     *
     * Rescan the blockchain from scratch
     *
     *
     *
     */

    public function rescan_blockchain()
    {
        return $this->_run('rescan_blockchain');
    }

    /**
     *
     * Add notes to transactions
     *
     * @param array $txids Array of transaction IDs to note
     * @param array $notes Array of notes (strings) to add
     *
     *
     */
    public function set_tx_notes(array $txids, array $notes)
    {
        $params = array('txids' => $txids, 'notes' => $notes);
        return $this->_run('set_tx_notes', $params);
    }

    /**
     *
     * Look up transaction note
     *
     * @param array $txids Array of transaction IDs (strings) to look up
     *
     * @return array  Example: {
     *   // TODO example
     * }
     *
     */
    public function get_tx_notes(array $txids): array
    {
        $params = array('txids' => $txids);
        return $this->_run('get_tx_notes', $params);
    }

    /**
     *
     * Set a wallet option
     *
     * @param string $key Option to set
     * @param string $value Value to set
     *
     *
     */
    public function set_attribute(string $key, string $value)
    {
        $params = array('key' => $key, 'value' => $value);
        return $this->_run('set_attribute', $params);
    }

    /**
     *
     * Look up a wallet option
     *
     * @param string $key Wallet option to query
     *
     * @return array  Example: {
     *   // TODO example
     * }
     *
     */
    public function get_attribute(string $key): array
    {
        $params = array('key' => $key);
        return $this->_run('get_attribute', $params);
    }

    /**
     *
     * Look up a transaction key
     *
     * @param string $txid Transaction ID to look up
     *
     * @return  array  Example: {
     *   "tx_key": "e8e97866b1606bd87178eada8f995bf96d2af3fec5db0bc570a451ab1d589b0f"
     * }
     *
     */
    public function get_tx_key(string $txid): array
    {
        $params = array('txid' => $txid);
        return $this->_run('get_tx_key', $params);
    }

    /**
     *
     * Check a transaction key
     *
     * @param string $address Address that sent transaction
     * @param string $txid Transaction ID
     * @param string $tx_key Transaction key
     *
     * @return  array  Example: {
     *   "confirmations": 1,
     *   "in_pool": ,
     *   "received": 0
     * }
     *
     */
    public function check_tx_key(string $address, string $txid, string $tx_key): array
    {
        $params = array('address' => $address, 'txid' => $txid, 'tx_key' => $tx_key);
        return $this->_run('check_tx_key', $params);
    }

    /**
     *
     * Create proof (signature) of transaction
     *
     * @param string $address Address that spent funds
     * @param string $txid Transaction ID
     *
     * @return array  Example: {
     *   "signature": "InProofV1Lq4nejMXxMnAdnLeZhHe3FGCmFdnSvzVM1AiGcXjngTRi4hfHPcDL9D4th7KUuvF9ZHnzCDXysNBhfy7gFvUfSbQWiqWtzbs35yUSmtW8orRZzJpYKNjxtzfqGthy1U3puiF"
     * }
     *
     */
    public function get_tx_proof(string $address, string $txid): array
    {
        $params = array('address' => $address, 'txid' => $txid);
        return $this->_run('get_tx_proof', $params);
    }

    /**
     *
     * Verify transaction proof
     *
     * @param string $address Address that spent funds
     * @param string $txid Transaction ID
     * @param string $signature Signature (tx_proof)
     *
     * @return array  Example: {
     *   "confirmations": 2,
     *   "good": 1,
     *   "in_pool": ,
     *   "received": 15752471409492,
     * }
     *
     */
    public function check_tx_proof(string $address, string $txid, string $signature): array
    {
        $params = array('address' => $address, 'txid' => $txid, 'signature' => $signature);
        return $this->_run('check_tx_proof', $params);
    }

    /**
     *
     * Create proof of a spend
     *
     * @param string $txid Transaction ID
     *
     * @return array  Example: {
     *   "signature": "SpendProofV1RnP6ywcDQHuQTBzXEMiHKbe5ErzRAjpUB1h4RUMfGPNv4bbR6V7EFyiYkCrURwbbrYWWxa6Kb38ZWWYTQhr2Y1cRHVoDBkK9GzBbikj6c8GWyKbu3RKi9hoYp2fA9zze7UEdeNrYrJ3tkoE6mkR3Lk5HP6X2ixnjhUTG65EzJgfCS4qZ85oGkd17UWgQo6fKRC2GRgisER8HiNwsqZdUTM313RmdUX7AYaTUNyhdhTinVLuaEw83L6hNHANb3aQds5CwdKCUQu4pkt5zn9K66z16QGDAXqL6ttHK6K9TmDHF17SGNQVPHzffENLGUf7MXqS3Pb6eijeYirFDxmisZc1n2mh6d5EW8ugyHGfNvbLEd2vjVPDk8zZYYr7NyJ8JjaHhDmDWeLYy27afXC5HyWgJH5nDyCBptoCxxDnyRuAnNddBnLsZZES399zJBYHkGb197ZJm85TV8SRC6cuYB4MdphsFdvSzygnjFtbAcZWHy62Py3QCTVhrwdUomAkeNByM8Ygc1cg245Se1V2XjaUyXuAFjj8nmDNoZG7VDxaD2GT9dXDaPd5dimCpbeDJEVoJXkeEFsZF85WwNcd67D4s5dWySFyS8RbsEnNA5UmoF3wUstZ2TtsUhiaeXmPwjNvnyLif3ASBmFTDDu2ZEsShLdddiydJcsYFJUrN8L37dyxENJN41RnmEf1FaszBHYW1HW13bUfiSrQ9sLLtqcawHAbZWnq4ZQLkCuomHaXTRNfg63hWzMjdNrQ2wrETxyXEwSRaodLmSVBn5wTFVzJe5LfSFHMx1FY1xf8kgXVGafGcijY2hg1yw8ru9wvyba9kdr16Lxfip5RJGFkiBDANqZCBkgYcKUcTaRc1aSwHEJ5m8umpFwEY2JtakvNMnShjURRA3yr7GDHKkCRTSzguYEgiFXdEiq55d6BXDfMaKNTNZzTdJXYZ9A2j6G9gRXksYKAVSDgfWVpM5FaZNRANvaJRguQyqWRRZ1gQdHgN4DqmQ589GPmStrdfoGEhk1LnfDZVwkhvDoYfiLwk9Z2JvZ4ZF4TojUupFQyvsUb5VPz2KNSzFi5wYp1pqGHKv7psYCCodWdte1waaWgKxDken44AB4k6wg2V8y1vG7Nd4hrfkvV4Y6YBhn6i45jdiQddEo5Hj2866MWNsdpmbuith7gmTmfat77Dh68GrRukSWKetPBLw7Soh2PygGU5zWEtgaX5g79FdGZg"
     * }
     *
     */
    public function get_spend_proof(string $txid): array
    {
        $params = array('txid' => $txid);
        return $this->_run('get_spend_proof', $params);
    }

    /**
     *
     * Verify spend proof
     *
     * @param string $txid Transaction ID
     * @param string $signature Spend proof to verify
     *
     * @return array  Example: {
     *   "good": 1
     * }
     *
     */
    public function check_spend_proof(string $txid, string $signature): array
    {
        $params = array('txid' => $txid, 'signature' => $signature);
        return $this->_run('check_spend_proof', $params);
    }

    /**
     *
     * Create proof of reserves
     *
     * @param string $account_index Comma-separated list of account indices of which to prove reserves (proves reserve of all accounts if empty)  (optional)
     *
     * @return  array Example: {
     *   "signature": "ReserveProofV11BZ23sBt9sZJeGccf84mzyAmNCP3KzYbE111111111111AjsVgKzau88VxXVGACbYgPVrDGC84vBU61Gmm2eiYxdZULAE4yzBxT1D9epWgCT7qiHFvFMbdChf3CpR2YsZj8CEhp8qDbitsfdy7iBdK6d5pPUiMEwCNsCGDp8AiAc6sLRiuTsLEJcfPYEKe"
     * }
     *
     */
    public function get_reserve_proof(string $account_index = 'all'): array
    {
        if ($account_index == 'all') {
            $params = array('all' => true);
        } else {
            $params = array('account_index' => $account_index);
        }

        return $this->_run('get_reserve_proof');
    }

    /**
     *
     * Verify a reserve proof
     *
     * @param string $address Wallet address
     * @param string $signature Reserve proof
     *
     * @return array  Example: {
     *   "good": 1,
     *   "spent": 0,
     *   "total": 0
     * }
     *
     */
    public function check_reserve_proof(string $address, string $signature): array
    {
        $params = array('address' => $address, 'signature' => $signature);
        return $this->_run('check_reserve_proof', $params);
    }

    /**
     *
     * Look up transfers
     *
     * @param array{
     *     all: boolean,
     *     in: boolean,
     *     out: boolean,
     *     pending: boolean,
     *     failed: boolean,
     *     pool: boolean
     * } $inputTypes Array of transfer type strings;
     * @param int $accountIndex Index of account to look up
     * @param ?string $subAddressIndices Comma-seperated list of subaddress indices to look up
     * @param ?int $min_height Minimum block height to use when looking up transfers
     * @param ?int $max_height Maximum block height to use when looking up transfers
     *
     * @return array{
     *     pool: array{
     *          amount: float,
     *          fee: float,
     *          height: int,
     *          note: string,
     *          payment_id: string,
     *          timestamp: int,
     *          txid: string,
     *          type: string
     *      }[]
     * }  Example: {
     *   "pool": [{
     *     "amount": 500000000000,
     *     "fee": 0,
     *     "height": 0,
     *     "note": "",
     *     "payment_id": "758d9b225fda7b7f",
     *     "timestamp": 1488312467,
     *     "txid": "da7301d5423efa09fabacb720002e978d114ff2db6a1546f8b820644a1b96208",
     *     "type": "pool"
     *   }]
     * }
     */
    public function getTransfers(
        array   $inputTypes,
        int     $accountIndex,
        ?string $subAddressIndices = null,
        ?int    $min_height = null,
        ?int    $max_height = null
    ): array
    {
        if (array_key_exists('all', $inputTypes)) {
            unset($inputTypes['all']);
            $inputTypes['in'] = true;
            $inputTypes['out'] = true;
            $inputTypes['pending'] = true;
            $inputTypes['failed'] = true;
            $inputTypes['pool'] = true;
        }

        $params = [
            ...$inputTypes,
            'account_index' => $accountIndex,
            'subaddr_indices' => $subAddressIndices ?? '',
            'min_height' => $min_height ?? 0,
            'max_height' => $max_height ?? 0
        ];

        if ($min_height && $max_height) {
            $params['filter_by_height'] = true;
        }

        return $this->_run('get_transfers', $params);
    }

    /**
     *
     * Look up transaction by transaction ID
     *
     * @param string $txId Transaction ID to look up
     * @param int $accountIndex Index of account to query  (optional)
     *
     * @return array  Example: {
     *   "transfer": {
     *     "amount": 10000000000000,
     *     "fee": 0,
     *     "height": 1316388,
     *     "note": "",
     *     "payment_id": "0000000000000000",
     *     "timestamp": 1495539310,
     *     "txid": "f2d33ba969a09941c6671e6dfe7e9456e5f686eca72c1a94a3e63ac6d7f27baf",
     *     "type": "in"
     *   }
     * }
     *
     */
    public function getTransferByTxId(string $txId, int $accountIndex): array
    {
        return $this->_run('get_transfer_by_txid', [
            'txid' => $txId,
            'account_index' => $accountIndex
        ]);
    }

    /**
     * Sign a string
     *
     * @param string $data Data to sign
     *
     * @return array  Example: {
     *   "signature": "SigV1Xp61ZkGguxSCHpkYEVw9eaWfRfSoAf36PCsSCApx4DUrKWHEqM9CdNwjeuhJii6LHDVDFxvTPijFsj3L8NDQp1TV"
     * }
     */
    public function sign(string $data): array
    {
        $params = array('string' => $data);
        return $this->_run('sign', $params);
    }

    /**
     * Verify a signature
     *
     * @param string $data Signed data
     * @param string $address Address that signed data
     * @param string $signature Signature to verify
     *
     * @return array  Example: {
     *   "good": true
     * }
     */
    public function verify(string $data, string $address, string $signature): array
    {
        $params = array('data' => $data, 'address' => $address, 'signature' => $signature);
        return $this->_run('verify', $params);
    }

    /**
     * Export an array of signed key images
     *
     *
     * @return array  Example: {
     *   // TODO example
     * }
     */
    public function export_key_images(): array
    {
        return $this->_run('export_key_images');
    }

    /**
     *
     * Import a signed set of key images
     *
     * @param array $signed_key_images Array of signed key images
     *
     * @return array  Example: {
     *   // TODO example
     *   height: ,
     *   spent: ,
     *   unspent:
     * }
     *
     */
    public function import_key_images(array $signed_key_images): array
    {
        $params = array('signed_key_images' => $signed_key_images);
        return $this->_run('import_key_images', $params);
    }

    /**
     * Create a payment URI using the official URI specification
     *
     * @param string $address Address to receive fuids
     * @param string $amount Amount of monero to request
     * @param string|null $payment_id Payment ID                   (optional)
     * @param string|null $recipient_name Name of recipient            (optional)
     * @param string|null $tx_description Payment description          (optional)
     *
     * @return array  Example: {
     *   // TODO example
     * }
     */
    public function make_uri(
        string  $address,
        string  $amount,
        ?string $payment_id = null,
        ?string $recipient_name = null,
        ?string $tx_description = null
    ): array
    {
        return $this->_run('make_uri', [
            'address' => $address,
            'amount' => $this->_transform($amount),
            'payment_id' => $payment_id,
            'recipient_name' => $recipient_name,
            'tx_description' => $tx_description
        ]);
    }

    /**
     * Parse a payment URI
     *
     * @param string $uri Payment URI
     *
     * @return array  Example: {
     *   "uri": {
     *     "address": "44AFFq5kSiGBoZ4NMDwYtN18obc8AemS33DBLWs3H7otXft3XjrpDtQGv7SqSsaBYBb98uNbr2VBBEt7f2wfn3RVGQBEP3A",
     *     "amount": 10,
     *     "payment_id": "0123456789abcdef0123456789abcdef0123456789abcdef0123456789abcdef",
     *     "recipient_name": "Monero Project donation address",
     *     "tx_description": "Testing out the make_uri function"
     *   }
     * }
     */
    public function parse_uri(string $uri): array
    {
        return $this->_run('parse_uri', ['uri' => $uri]);
    }

    /**
     *
     * Look up address book entries
     *
     * @param array $entries Array of address book entry indices to look up
     *
     * @return array  Example: {
     *   // TODO example
     * }
     *
     */
    public function get_address_book(array $entries): array
    {
        $params = array('entries' => $entries);
        return $this->_run('get_address_book', $params);
    }

    /**
     * Add entry to the address book
     *
     * @param string $address Address to add to address book
     * @param string $payment_id Payment ID to use with address in address book  (optional)
     * @param string $description Description of address                          (optional)
     *
     * @return array  Example: {
     *   // TODO example
     * }
     */
    public function add_address_book(string $address, string $payment_id, string $description): array
    {
        $params = array('address' => $address, 'payment_id' => $payment_id, 'description' => $description);
        return $this->_run('add_address_book', $params);
    }

    /**
     * Delete an entry from the address book
     *
     * @param array $index Index of the address book entry to remove
     */
    public function delete_address_book(array $index): void
    {
        $this->_run('delete_address_book', ['index' => $index]);
    }

    /**
     * Rescan the blockchain for spent outputs
     */
    public function rescan_spent()
    {
        return $this->_run('rescan_spent');
    }

    /**
     * Start mining
     *
     * @param int $threads_count Number of threads with which to mine
     * @param boolean $do_background_mining Mine in backgound?
     * @param boolean $ignore_battery Ignore battery?
     */
    public function start_mining(int $threads_count, bool $do_background_mining, bool $ignore_battery)
    {
        return $this->_run('start_mining', [
            'threads_count' => $threads_count,
            'do_background_mining' => $do_background_mining,
            'ignore_battery' => $ignore_battery
        ]);
    }

    /**
     * Stop mining
     */
    public function stop_mining()
    {
        return $this->_run('stop_mining');
    }

    /**
     *
     * Look up a list of available languages for your wallet's seed
     *
     *
     * @return array  Example: {
     *   // TODO example
     * }
     *
     */
    public function get_languages(): array
    {
        return $this->_run('get_languages');
    }

    /**
     * Create a new wallet
     *
     * @param string $filename Filename of new wallet to create
     * @param ?string $password Password of new wallet to create
     * @param string $language Language of new wallet to create
     */
    public function create_wallet(
        string  $filename = 'monero_wallet',
        ?string $password = null,
        string  $language = 'English'
    )
    {
        $params = array('filename' => $filename, 'password' => $password, 'language' => $language);
        return $this->_run('create_wallet', $params);
    }

    /**
     * Open a wallet
     *
     * @param string $filename Filename of wallet to open
     * @param string|null $password Password of wallet to open
     */
    public function open_wallet(string $filename = 'monero_wallet', ?string $password = null)
    {
        $params = array('filename' => $filename, 'password' => $password);
        return $this->_run('open_wallet', $params);
    }

    /**
     *
     * Check if wallet is multisig
     *
     *
     * @return array  Example: (non-multisignature wallet) {
     *   "multisig": ,
     *   "ready": ,
     *   "threshold": 0,
     *   "total": 0
     * } // TODO multisig wallet example
     *
     */
    public function is_multisig(): array
    {
        return $this->_run('is_multisig');
    }

    /**
     *
     * Create information needed to create a multisignature wallet
     *
     *
     * @return array  Example: {
     *   "multisig_info": "MultisigV1WBnkPKszceUBriuPZ6zoDsU6RYJuzQTiwUqE5gYSAD1yGTz85vqZGetawVvioaZB5cL86kYkVJmKbXvNrvEz7o5kibr7tHtenngGUSK4FgKbKhKSZxVXRYjMRKEdkcbwFBaSbsBZxJFFVYwLUrtGccSihta3F4GJfYzbPMveCFyT53oK"
     * }
     *
     */
    public function prepare_multisig(): array
    {
        return $this->_run('prepare_multisig');
    }

    /**
     *
     * Create a multisignature wallet
     *
     * @param string $multisig_info Multisignature information (from e.g. prepare_multisig)
     * @param string $threshold Threshold required to spend from multisignature wallet
     * @param string $password Passphrase to apply to multisignature wallet
     *
     * @return array  Example: {
     *   // TODO example
     * }
     *
     */
    public function make_multisig(string $multisig_info, string $threshold, string $password = ''): array
    {
        $params = array('multisig_info' => $multisig_info, 'threshold' => $threshold, 'password' => $password);
        return $this->_run('make_multisig', $params);
    }

    /**
     *
     * Export multisignature information
     *
     *
     * @return array  Example: {
     *   // TODO example
     * }
     *
     */
    public function export_multisig_info(): array
    {
        return $this->_run('export_multisig_info');
    }

    /**
     *
     * Import mutlisignature information
     *
     * @param string $info Multisignature info (from eg. prepare_multisig)
     *
     * @return  array Example: {
     *   // TODO example
     * }
     *
     */
    public function import_multisig_info(string $info): array
    {
        $params = array('info' => $info);
        return $this->_run('import_multisig_info', $params);
    }

    /**
     *
     * Finalize a multisignature wallet
     *
     * @param string $multisig_info Multisignature info (from eg. prepare_multisig)
     * @param string $password Multisignature info (from eg. prepare_multisig)
     *
     * @return  array Example: {
     *   // TODO example
     * }
     *
     */
    public function finalize_multisig(string $multisig_info, string $password = ''): array
    {
        $params = array('multisig_info' => $multisig_info, 'password' => $password);
        return $this->_run('finalize_multisig', $params);
    }

    /**
     *
     * Sign a multisignature transaction
     *
     * @param string $tx_data_hex Blob of transaction to sign
     *
     * @return array  Example: {
     *   // TODO example
     * }
     *
     */
    public function sign_multisig(string $tx_data_hex): array
    {
        $params = array('tx_data_hex' => $tx_data_hex);
        return $this->_run('sign_multisig', $params);
    }

    /**
     *
     * Submit (relay) a multisignature transaction
     *
     * @param string $tx_data_hex Blob of transaction to submit
     *
     * @return  array Example: {
     *   // TODO example
     * }
     *
     */
    public function submit_multisig(string $tx_data_hex): array
    {
        $params = array('tx_data_hex' => $tx_data_hex);
        return $this->_run('submit_multisig', $params);
    }

}
