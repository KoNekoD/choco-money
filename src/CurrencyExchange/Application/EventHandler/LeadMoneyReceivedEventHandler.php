<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Application\EventHandler;

use App\CurrencyExchange\Domain\CurrencyAPI\DTO\TransferRequestDTO;
use App\CurrencyExchange\Domain\Event\LeadMoneyReceivedEvent;
use App\CurrencyExchange\Domain\Exception\CurrencyApiNotFoundException;
use App\CurrencyExchange\Domain\Exception\CurrencyExchangeException;
use App\CurrencyExchange\Domain\Exception\DelayedTransferNotFoundException;
use App\CurrencyExchange\Domain\Repository\DelayedTransferRepositoryInterface;
use App\CurrencyExchange\Infrastructure\Provider\CurrencyApiProvider;
use App\CurrencyExchange\Infrastructure\Service\ExchangeCalculationService;
use App\Shared\Domain\Event\EventHandlerInterface;

class LeadMoneyReceivedEventHandler implements EventHandlerInterface
{
    public function __construct(
        private readonly DelayedTransferRepositoryInterface $delayedTransferRepository,
        private readonly CurrencyApiProvider                $currencyApiProvider,
        private readonly ExchangeCalculationService         $exchangeCalculationService
    )
    {
    }

    /**
     * @throws DelayedTransferNotFoundException
     * @throws CurrencyApiNotFoundException
     * @throws CurrencyExchangeException
     */
    public function __invoke(LeadMoneyReceivedEvent $event): void
    {
        $transfer = $this->delayedTransferRepository->findOne($event->transferId);

        $quoteApi = $this->currencyApiProvider->getApiByAsset(
            $transfer->getQuoteAsset()
        );

        $amount = $this->exchangeCalculationService->calculateCurrencyExchangePrice(
            $transfer->getExchangeSnapshot()->toDTO($transfer->getBaseAsset()),
            $transfer->getLeadBaseExchangeAmount()
        );

        $mutualTransfer = $quoteApi->transfer(
            new TransferRequestDTO(
                $quoteApi->getWalletByName($transfer->getExchangerBaseWalletName()),
                $transfer->getLeadQuoteWalletAddress(),
                $amount
            )
        );

        $transfer->convertToMutualMoneySent($mutualTransfer);
    }
}
