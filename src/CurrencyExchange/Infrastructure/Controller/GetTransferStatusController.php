<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Controller;

use App\CurrencyExchange\Domain\Enum\DelayedTransferStatusEnum;
use App\CurrencyExchange\Domain\Exception\DelayedTransferNotFoundException;
use App\CurrencyExchange\Domain\Repository\DelayedTransferRepositoryInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route(
        '/api/transfers/{transferId}/status',
        'api_transfer_get',
        methods: ['GET']
    ),
    OA\Tag(name: 'CurrencyExchange'),
    OA\Response(
        response: 200,
        description: 'Transfer item',
        content: new OA\JsonContent(
            ref: new Model(type: DelayedTransferStatusEnum::class)
        ),
    )
]
class GetTransferStatusController
{
    public function __construct(
        private readonly DelayedTransferRepositoryInterface $delayedTransferRepository,
    )
    {
    }

    /** @throws DelayedTransferNotFoundException */
    public function __invoke(string $transferId): JsonResponse
    {
        $transfer = $this->delayedTransferRepository->findOne($transferId);

        return new JsonResponse(
            $transfer->getStatus()
        );
    }
}
