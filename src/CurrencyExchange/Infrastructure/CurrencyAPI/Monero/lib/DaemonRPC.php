<?php
/**
 * A class for making calls to a Monero daemon's RPC API using PHP
 *
 * // Initialize class
 * $DaemonRPC = new DaemonRPC();
 *
 * // Examples:
 * $height = $DaemonRPC->getblockcount();
 * $block = $DaemonRPC->getblock_by_height(1);
 */

namespace App\CurrencyExchange\Infrastructure\CurrencyAPI\Monero\lib;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class DaemonRPC
{
    private JsonRPCClient $client;

    private string $url;
    private ?string $user;
    private ?string $password;

    /**
     * Start a connection with the Monero daemon (monerod)
     *
     * @param string $host Monero daemon IP hostname            (optional)
     * @param int $port Monero daemon port                   (optional)
     * @param string $protocol Monero daemon protocol (eg. 'http')  (optional)
     * @param string|null $user Monero daemon RPC username           (optional)
     * @param string|null $password Monero daemon RPC passphrase         (optional)
     *
     */
    function __construct(
        string              $host,
        int                 $port,
        string              $protocol,
        ?string             $user,
        ?string             $password,
        HttpClientInterface $httpClient,

    )
    {
        $this->user = $user;
        $this->password = $password;

        $this->url = $protocol . '://' . $host . ':' . $port . '/json_rpc';
        $this->client = new JsonRPCClient($this->url, $this->user, $this->password, $httpClient);
    }

    /**
     * Look up how many blocks are in the longest chain known to the node
     *
     * @return array<string, string>  Example: {
     *   "count": 993163,
     *   "status": "OK"
     * }
     *
     */
    public function getblockcount(): array
    {
        return $this->_run('getblockcount');
    }

    /**
     * Execute command via JsonRPCClient
     *
     * @param string $method RPC method to call
     * @param mixed $params Parameters to pass  (optional)
     *
     * @return mixed  Call result
     *
     */
    protected function _run(string $method, mixed $params = null): mixed
    {
        return $this->client->_run($method, $params);
    }

    /**
     * Look up a block's hash by its height
     *
     * @param float|int $height Height of block to look up
     *
     * @return array<string>  Example: 'e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6'
     */
    public function on_getblockhash(float|int $height): array
    {
        $params = array($height);

        return $this->_run('on_getblockhash', $params);
    }

    /**
     * Construct a block template that can be mined upon
     *
     * @param string $wallet_address Address of wallet to receive coinbase transactions if block is successfully mined
     * @param int $reserve_size Reserve size
     *
     * @return array<string, string|int>  Example: {
     *   "blocktemplate_blob": "01029af88cb70568b84a11dc9406ace9e635918ca03b008f7728b9726b327c1b482a98d81ed83000000000018bd03c01ffcfcf3c0493d7cec7020278dfc296544f139394e5e045fcda1ba2cca5b69b39c9ddc90b7e0de859fdebdc80e8eda1ba01029c5d518ce3cc4de26364059eadc8220a3f52edabdaf025a9bff4eec8b6b50e3d8080dd9da417021e642d07a8c33fbe497054cfea9c760ab4068d31532ff0fbb543a7856a9b78ee80c0f9decfae01023ef3a7182cb0c260732e7828606052a0645d3686d7a03ce3da091dbb2b75e5955f01ad2af83bce0d823bf3dbbed01ab219250eb36098c62cbb6aa2976936848bae53023c00000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000000001f12d7c87346d6b84e17680082d9b4a1d84e36dd01bd2c7f3b3893478a8d88fb3",
     *   "difficulty": 982540729,
     *   "height": 993231,
     *   "prev_hash": "68b84a11dc9406ace9e635918ca03b008f7728b9726b327c1b482a98d81ed830",
     *   "reserved_offset": 246,
     *   "status": "OK"
     * }
     *
     */
    public function getblocktemplate(string $wallet_address, int $reserve_size): array
    {
        $params = array('wallet_address' => $wallet_address, 'reserve_size' => $reserve_size);

        return $this->_run('getblocktemplate', $params);
    }

    /**
     * Submit a mined block to the network
     *
     * @param string $block Block blob
     *
     * @return array<string, string>
     */
    public function submitblock(string $block): array
    {
        return $this->_run('submitblock', $block);
    }

    /**
     * Look up the block header of the latest block in the longest chain known to the node
     *
     * @return array<array<string,string>|string, string>  Example: {
     *   "block_header": {
     *     "depth": 0,
     *     "difficulty": 746963928,
     *     "hash": "ac0f1e226268d45c99a16202fdcb730d8f7b36ea5e5b4a565b1ba1a8fc252eb0",
     *     "height": 990793,
     *     "major_version": 1,
     *     "minor_version": 1,
     *     "nonce": 1550,
     *     "orphan_status": false,
     *     "prev_hash": "386575e3b0b004ed8d458dbd31bff0fe37b280339937f971e06df33f8589b75c",
     *     "reward": 6856609225169,
     *     "timestamp": 1457589942
     *   },
     *   "status": "OK"
     * }
     *
     */
    public function getlastblockheader(): array
    {
        return $this->_run('getlastblockheader');
    }

    /**
     * Look up a block header from a block hash
     *
     * @param string $hash The block's SHA256 hash
     *
     * @return array<array<string,string>|string, string>  Example: {
     *   "block_header": {
     *     "depth": 78376,
     *     "difficulty": 815625611,
     *     "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
     *     "height": 912345,
     *     "major_version": 1,
     *     "minor_version": 2,
     *     "nonce": 1646,
     *     "orphan_status": false,
     *     "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
     *     "reward": 7388968946286,
     *     "timestamp": 1452793716
     *   },
     *   "status": "OK"
     * }
     *
     */
    public function getblockheaderbyhash(string $hash): array
    {
        $params = array('hash' => $hash);

        return $this->_run('getblockheaderbyhash', $params);
    }

    /**
     * Look up a block header by height
     *
     * @param int $height Height of block
     *
     * @return array<array<string,string>|string, string>  Example: {
     *   "block_header": {
     *     "depth": 78376,
     *     "difficulty": 815625611,
     *     "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
     *     "height": 912345,
     *     "major_version": 1,
     *     "minor_version": 2,
     *     "nonce": 1646,
     *     "orphan_status": false,
     *     "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
     *     "reward": 7388968946286,
     *     "timestamp": 1452793716
     *   },
     *   "status": "OK"
     * }
     *
     */
    public function getblockheaderbyheight(int $height): array
    {
        return $this->_run('getblockheaderbyheight', $height);
    }

    /**
     * Look up block information by SHA256 hash
     *
     * @param string $hash SHA256 hash of block
     *
     * @return array<array<string,string>|string, string>  Example: {
     *   "blob": "...",
     *   "block_header": {
     *     "depth": 12,
     *     "difficulty": 964985344,
     *     "hash": "510ee3c4e14330a7b96e883c323a60ebd1b5556ac1262d0bc03c24a3b785516f",
     *     "height": 993056,
     *     "major_version": 1,
     *     "minor_version": 2,
     *     "nonce": 2036,
     *     "orphan_status": false,
     *     "prev_hash": "0ea4af6547c05c965afc8df6d31509ff3105dc7ae6b10172521d77e09711fd6d",
     *     "reward": 6932043647005,
     *     "timestamp": 1457720227
     *   },
     *   "json": "...",
     *   "status": "OK"
     * }
     *
     */
    public function getblock_by_hash(string $hash): array
    {
        $params = array('hash' => $hash);

        return $this->_run('getblock', $params);
    }

    /**
     * Look up block information by height
     *
     * @param int $height Height of block
     *
     * @return array<array<string,string>|string, string>  Example: {
     *   "blob": "...",
     *   "block_header": {
     *     "depth": 80694,
     *     "difficulty": 815625611,
     *     "hash": "e22cf75f39ae720e8b71b3d120a5ac03f0db50bba6379e2850975b4859190bc6",
     *     "height": 912345,
     *     "major_version": 1,
     *     "minor_version": 2,
     *     "nonce": 1646,
     *     "orphan_status": false,
     *     "prev_hash": "b61c58b2e0be53fad5ef9d9731a55e8a81d972b8d90ed07c04fd37ca6403ff78",
     *     "reward": 7388968946286,
     *     "timestamp": 1452793716
     *   },
     *   "json": "...",
     *   "status": "OK"
     * }
     *
     */
    public function getblock_by_height(int $height): array
    {
        $params = array('height' => $height);

        return $this->_run('getblock', $params);
    }

    /**
     * Look up incoming and outgoing connections to your node
     *
     *
     * @return array<array<string,string>|string, string>  Example: {
     *   "connections": [{
     *     "avg_download": 0,
     *     "avg_upload": 0,
     *     "current_download": 0,
     *     "current_upload": 0,
     *     "incoming": false,
     *     "ip": "76.173.170.133",
     *     "live_time": 1865,
     *     "local_ip": false,
     *     "localhost": false,
     *     "peer_id": "3bfe29d6b1aa7c4c",
     *     "port": "18080",
     *     "recv_count": 116396,
     *     "recv_idle_time": 23,
     *     "send_count": 176893,
     *     "send_idle_time": 1457726610,
     *     "state": "state_normal"
     *   },{
     *   ..
     *   }],
     *   "status": "OK"
     * }
     *
     */
    public function get_connections(): array
    {
        return $this->_run('get_connections');
    }

    /**
     * Look up general information about the state of your node and the network
     *
     * @return array<array<string,string>|string, string>  Example: {
     *   "alt_blocks_count": 5,
     *   "difficulty": 972165250,
     *   "grey_peerlist_size": 2280,
     *   "height": 993145,
     *   "incoming_connections_count": 0,
     *   "outgoing_connections_count": 8,
     *   "status": "OK",
     *   "target": 60,
     *   "target_height": 993137,
     *   "testnet": false,
     *   "top_block_hash": "",
     *   "tx_count": 564287,
     *   "tx_pool_size": 45,
     *   "white_peerlist_size": 529
     * }
     *
     */
    public function get_info(): array
    {
        return $this->_run('get_info');
    }

    /**
     * Look up information regarding hard fork voting and readiness
     *
     * @return array<string, string>  Example:
     * {
     *   "alt_blocks_count": 0,
     *   "block_size_limit": 600000,
     *   "block_size_median": 85,
     *   "bootstrap_daemon_address": ?,
     *   "cumulative_difficulty": 40859323048,
     *   "difficulty": 57406,
     *   "free_space": 888592449536,
     *   "grey_peerlist_size": 526,
     *   "height": 1066107,
     *   "height_without_bootstrap": 1066107,
     *   "incoming_connections_count": 1,
     *   "offline":  ?,
     *   "outgoing_connections_count": 1,
     *   "rpc_connections_count": 1,
     *   "start_time": 1519963719,
     *   "status": OK,
     *   "target": 120,
     *   "target_height": 1066073,
     *   "testnet": 1,
     *   "top_block_hash": e438aae56de8e5e5c8e0d230167fcb58bc8dde09e369ff7689a4af146040a20e,
     *   "tx_count": 52632,
     *   "tx_pool_size": 0,
     *   "untrusted": ?,
     *   "was_bootstrap_ever_used: ?,
   *   "white_peerlist_size": 5
     * }
     */
    public function hardfork_info(): array
    {
        return $this->_run('hard_fork_info');
    }

    /**
     * Ban another node by IP
     *
     * @param string[] $bans Array of IP addresses to ban
     *
     * @return array<string, string>  Example: {
     *   "status": "OK"
     * }
     *
     */
    public function setBans(array $bans): array
    {
        return $this->_run('set_bans', ['bans' => $bans]);
    }

    /**
     * Get list of banned IPs
     *
     * @return array<array<string, string>|string, string>  Example: {
     *   "bans": [{
     *     "ip": 838969536,
     *     "seconds": 1457748792
     *   }],
     *   "status": "OK"
     * }
     *
     */
    public function getBans(): array
    {
        return $this->_run('get_bans');
    }
}
