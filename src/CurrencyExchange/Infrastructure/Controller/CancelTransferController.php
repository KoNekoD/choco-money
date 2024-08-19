<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Controller;

use App\CurrencyExchange\Domain\Exception\DelayedTransferNotFoundException;
use App\CurrencyExchange\Domain\Repository\DelayedTransferRepositoryInterface;
use App\CurrencyExchange\Infrastructure\DTO\CancelTransferRequestDTO;
use App\Shared\Domain\Service\SerializerServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route(
        '/api/transfers/{transferId}/cancel',
        'api_transfer_cancel',
        methods: ['POST']
    ),
    OA\Tag(name: 'CurrencyExchange'),
    OA\RequestBody(
        content: new OA\JsonContent(
            ref: new Model(type: CancelTransferRequestDTO::class),
            type: 'object'
        )
    ),
    OA\Response(
        response: 204,
        description: 'Transfer cancelled',
    )
]
class CancelTransferController
{
    public function __construct(
        private readonly SerializerServiceInterface         $serializerService,
        private readonly DelayedTransferRepositoryInterface $delayedTransferRepository
    )
    {
    }

    /** @throws DelayedTransferNotFoundException */
    public function __invoke(Request $request, string $transferId): Response
    {
        $DTO = $this->serializerService->deserialize(
            $request->getContent(),
            CancelTransferRequestDTO::class
        );

        $transfer = $this->delayedTransferRepository->findOne($transferId);

        $transfer->convertToCancelled($DTO->reason);

        return new Response(status: Response::HTTP_NO_CONTENT);
    }
}
