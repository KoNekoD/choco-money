<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Console;

use App\CurrencyExchange\Domain\Exception\CurrencyApiNotFoundException;
use App\CurrencyExchange\Domain\Repository\DelayedTransferRepositoryInterface;
use App\CurrencyExchange\Domain\Repository\ExchangeSnapshotRepositoryInterface;
use App\CurrencyExchange\Domain\Repository\ExchangeSymbolRepositoryInterface;
use App\CurrencyExchange\Infrastructure\Provider\CurrencyApiProvider;
use App\Shared\Domain\Service\UlidService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand('app:exchange:transfers:check-money-received')]
class CheckMoneyReceivedTransfersConsole extends Command
{
    public function __construct(
        private readonly DelayedTransferRepositoryInterface  $delayedTransferRepository,
        private readonly CurrencyApiProvider                 $currencyApiProvider,
        private readonly ExchangeSnapshotRepositoryInterface $exchangeSnapshotRepository,
        private readonly ExchangeSymbolRepositoryInterface   $exchangeSymbolRepository,
    )
    {
        parent::__construct();
    }

    /** @throws CurrencyApiNotFoundException */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        echo UlidService::generate();
        foreach ($this->delayedTransferRepository->findPendingTransfers() as $transfer) {

            $baseAssetApi = $this->currencyApiProvider->getApiByAsset(
                $transfer->getBaseAsset()
            );

            $beforeReceiveMoneyAmount = $transfer->getExchangerBaseWalletBalanceAmountBeforeReceiveMoney();
            $currentAmount = $baseAssetApi->getBalance(
                $baseAssetApi->getWalletByName($transfer->getExchangerBaseWalletName()),
            )->unlockedBalance;

            $neededToSuccess = $beforeReceiveMoneyAmount + $transfer->getLeadBaseExchangeAmount();
            if ($neededToSuccess === $currentAmount) {
                $symbol = $this->exchangeSymbolRepository->findOneByAssets(
                    $transfer->getBaseAsset(), $transfer->getQuoteAsset()
                );
                $snapshot = $this->exchangeSnapshotRepository->getLastByCurrencyAssets($symbol);
                $transfer->convertToMoneyReceived($snapshot);
            }
        }

        $this->delayedTransferRepository->save();
        return Command::SUCCESS;
    }
}
