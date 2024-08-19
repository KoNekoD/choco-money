<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\CurrencyDataCollector;

use App\CurrencyExchange\Domain\CurrencyDataCollector\CurrencyDataCollectorInterface;
use App\CurrencyExchange\Domain\DTO\ExchangeInfoDTO;
use App\CurrencyExchange\Domain\Entity\ExchangeSymbol;
use App\Shared\Domain\Service\SerializerServiceInterface;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BinanceClient implements CurrencyDataCollectorInterface
{
    private readonly HttpClientInterface $httpClient;

    public function __construct(
        HttpClientInterface                         $httpClient,
        private readonly SerializerServiceInterface $serializerService,
    )
    {
        $this->httpClient = $httpClient->withOptions([
            'base_uri' => 'https://api.binance.com',
            'headers' => ['X-MBX-APIKEY' => $_SERVER['BINANCE_API_KEY']]
        ]);
    }

    /** @throws ExceptionInterface */
    public function getAvgPrice(ExchangeSymbol $symbol): float
    {
        $response = $this->httpClient->request(
            'GET',
            '/api/v3/avgPrice',
            [
                'query' => [
                    'symbol' => $symbol->getSymbol()
                ]
            ]
        );

        $result = $response->toArray(false);


        return (float)$result['price'];
    }

    /** @throws ExceptionInterface */
    public function exchangeInfo(): ExchangeInfoDTO
    {
        $response = $this->httpClient->request(
            'GET',
            '/api/v3/exchangeInfo'
        );

        return $this->serializerService->deserialize($response->getContent(), ExchangeInfoDTO::class);
    }
}
