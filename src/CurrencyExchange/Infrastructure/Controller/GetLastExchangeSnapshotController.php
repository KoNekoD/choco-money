<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Controller;

use App\CurrencyExchange\Domain\Exception\CurrencyExchangeException;
use App\CurrencyExchange\Infrastructure\DTO\CurrencyExchangeSnapshotDTO;
use App\CurrencyExchange\Infrastructure\Service\ExchangeCalculationService;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route(
        '/api/currency/exchange/snapshots/lastByAssets/{baseAsset}/{quoteAsset}',
        'api_get_currency_exchange_snapshot',
        methods: ['GET']
    ),
    OA\Tag(name: 'CurrencyExchange'),
    OA\Response(
        response: 200,
        description: 'Get last currency exchange snapshot',
        content: new OA\JsonContent(
            ref: new Model(type: CurrencyExchangeSnapshotDTO::class)
        )
    )
]
class GetLastExchangeSnapshotController
{
    public function __construct(private readonly ExchangeCalculationService $exchangeCalculationService)
    {
    }

    /** @throws CurrencyExchangeException */
    public function __invoke(string $baseAsset, string $quoteAsset): JsonResponse
    {
        $exchangeRate = $this->exchangeCalculationService->calculateCurrencyExchange(
            $baseAsset, $quoteAsset
        );

        return new JsonResponse($exchangeRate);
    }
}
