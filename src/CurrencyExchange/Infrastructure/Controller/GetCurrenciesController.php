<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Controller;

use App\CurrencyExchange\Infrastructure\DTO\CurrencyDTO;
use App\CurrencyExchange\Infrastructure\Provider\CurrencyApiProvider;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route('/api/currencies/list', 'currencies_list', methods: ['GET']),
    OA\Tag(name: 'CurrencyExchange'),
    OA\Response(
        response: 200,
        description: 'Get list of currencies',
        content: new OA\JsonContent(
            type: 'array',
            items: new OA\Items(
                ref: new Model(type: CurrencyDTO::class)
            )
        )
    )
]
class GetCurrenciesController
{
    public function __construct(private readonly CurrencyApiProvider $apiProvider)
    {
    }

    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            CurrencyDTO::fromApisArray(
                $this->apiProvider->getList()
            )
        );
    }
}
