<?php

namespace App\CurrencyExchange\Infrastructure\CurrencyAPI\Monero\lib;

use InvalidArgumentException;
use RuntimeException;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class JsonRPCClient
{
    private ?string $url;
    private ?string $username;
    private ?string $password;

    /** @var array<int, int> $curl_options */
    private array $curl_options = array(
        CURLOPT_CONNECTTIMEOUT => 8,
        CURLOPT_TIMEOUT => 8
    );

    /** @var string[] $httpErrors */
    private array $httpErrors = array(
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        408 => '408 Request Timeout',
        500 => '500 Internal Server Error',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable'
    );

    public function __construct(string $pUrl, ?string $pUser, ?string $pPass, private readonly HttpClientInterface $httpClient)
    {
        $this->validate(false === extension_loaded('curl'), 'The curl extension must be loaded to use this class!');
        $this->validate(false === extension_loaded('json'), 'The json extension must be loaded to use this class!');

        $this->url = $pUrl;
        $this->username = $pUser;
        $this->password = $pPass;
    }

    public function validate($pFailed, $pErrMsg): void
    {
        if ($pFailed) {
            throw new RuntimeException($pErrMsg);
        }
    }

    public function setCurlOptions($pOptionsArray): static
    {
        if (is_array($pOptionsArray)) {
            $this->curl_options = $pOptionsArray + $this->curl_options;
        } else {
            throw new InvalidArgumentException('Invalid options type.');
        }
        return $this;
    }

    public function _run(string $pMethod, mixed $pParams): mixed
    {
        // generating unique id per process
        static $requestId = 0;
        $requestId++;

        $response = $this->httpClient->request(
            'POST',
            $this->url,
            [
                'json' => [
                    'jsonrpc' => '2.0',
                    'method' => $pMethod,
                    'params' => $pParams,
                    'id' => $requestId
                ],
//                'auth_ntlm' => [
//                    $this->username,
//                    $this->password
//                ],
//                'auth_basic' => [
//                    $this->username,
//                    $this->password
//                ],
                'verify_host' => false,
                'verify_peer' => false,
                'timeout' => 8,
                'max_duration' => 8,
            ]
        );
        $responseDecoded = $response->toArray();
//        $response = $this->getResponse(json_encode([
//                    'jsonrpc' => '2.0',
//                    'method' => $pMethod,
//                    'params' => $pParams,
//                    'id' => $requestId
//                ]));
//        $responseDecoded = json_decode($response, true);
        $this->validate(
            empty($responseDecoded['id']),
            'Invalid response data structure'
        );
        $this->validate(
            $responseDecoded['id'] != $requestId,
            'Request id: ' . $requestId . ' is different from Response id: ' . $responseDecoded['id']
        );
        if (isset($responseDecoded['error'])) {
            $errorMessage = 'Request have return error: ' . $responseDecoded['error']['message'] . '; ' . "\n" .
                'Request method: ' . $pMethod . '; ';
            if (isset($responseDecoded['error']['data'])) {
                $errorMessage .= "\n" . 'Error data: ' . $responseDecoded['error']['data'];
            }
            $this->validate($responseDecoded['error'], $errorMessage);
        }
        return $responseDecoded['result'];
    }

    /**
     * @deprecated Remove it
     */
    protected function getResponse(string $pRequest): string
    {
        // do the actual connection
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->url);//
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
        curl_setopt($ch, CURLOPT_USERPWD, $this->username . ":" . $this->password);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $pRequest);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if (!curl_setopt_array($ch, $this->curl_options)) {
            throw new RuntimeException('Error while setting curl options');
        }
        // send the request
        $response = curl_exec($ch);
        // check http status code
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if (isset($this->httpErrors[$httpCode])) {
            throw new RuntimeException('Response Http Error - ' . $this->httpErrors[$httpCode]);
        }
        // check for curl error
        if (0 < curl_errno($ch)) {
            throw new RuntimeException('Unable to connect to ' . $this->url . ' Error: ' . curl_error($ch));
        }
        // close the connection
        curl_close($ch);
        return $response;
    }

    private function getHttpErrorMessage($pErrorNumber): ?string
    {
        return $this->httpErrors[$pErrorNumber] ?? null;
    }
}
