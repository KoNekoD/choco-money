<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Controller;

use App\CurrencyExchange\Domain\Exception\CurrencyApiNotFoundException;
use App\CurrencyExchange\Domain\Exception\CurrencyExchangeException;
use App\CurrencyExchange\Domain\Repository\DelayedTransferRepositoryInterface;
use App\CurrencyExchange\Infrastructure\DTO\CreateTransferRequestDTO;
use App\CurrencyExchange\Infrastructure\DTO\TransferDTO;
use App\CurrencyExchange\Infrastructure\Factory\DelayedTransferFactory;
use App\Shared\Domain\Service\SerializerServiceInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[
    Route(
        '/api/transfers',
        'api_transfer_create',
        methods: ['POST']
    ),
    OA\Tag(name: 'CurrencyExchange'),
    OA\RequestBody(
        content: new OA\JsonContent(
            ref: new Model(type: CreateTransferRequestDTO::class)
        )
    ),
    OA\Response(
        response: 200,
        description: 'Transfer created',
        content: new OA\JsonContent(
            ref: new Model(type: TransferDTO::class)
        ),
    )
]
class CreateTransferController
{
    public function __construct(
        private readonly SerializerServiceInterface         $serializerService,
        private readonly DelayedTransferFactory             $delayedTransferFactory,
        private readonly DelayedTransferRepositoryInterface $delayedTransferRepository,
    )
    {
    }

    /**
     * @throws CurrencyApiNotFoundException
     * @throws CurrencyExchangeException
     */
    public function __invoke(Request $request): JsonResponse
    {
        $DTO = $this->serializerService->deserialize(
            $request->getContent(),
            CreateTransferRequestDTO::class
        );

        $transfer = $this->delayedTransferFactory->create($DTO);
        $this->delayedTransferRepository->add(
            $transfer, true
        );

        return new JsonResponse(
            TransferDTO::fromEntity($transfer),
            Response::HTTP_CREATED
        );
    }
}
