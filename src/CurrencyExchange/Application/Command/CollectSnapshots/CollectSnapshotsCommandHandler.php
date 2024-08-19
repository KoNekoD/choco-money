<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Application\Command\CollectSnapshots;

use App\CurrencyExchange\Domain\CurrencyDataCollector\CurrencyDataCollectorInterface;
use App\CurrencyExchange\Domain\Entity\ExchangeSnapshot;
use App\CurrencyExchange\Domain\Repository\ExchangeSnapshotRepositoryInterface;
use App\CurrencyExchange\Domain\Repository\ExchangeSymbolRepositoryInterface;
use App\Shared\Application\Command\CommandHandlerInterface;
use Carbon\Carbon;
use Symfony\Component\Lock\LockFactory;

class CollectSnapshotsCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private readonly LockFactory                         $lockFactory,
        private readonly ExchangeSymbolRepositoryInterface   $symbolRepository,
        private readonly CurrencyDataCollectorInterface      $currencyDataCollector,
        private readonly ExchangeSnapshotRepositoryInterface $exchangeSnapshotRepository
    )
    {
    }

    public function __invoke(CollectSnapshotsCommand $command): void
    {
        $lock = $this->lockFactory->createLock('collect_snapshots_command_handler', 600);
        if (!$lock->acquire()) {
            return;
        }

        $now = Carbon::now()->toDateTimeImmutable();
        $from = $now->modify('-' . ExchangeSnapshot::UPDATE_PERIOD);

        $symbols = $this->symbolRepository->getSymbolsForSnapshotByDateRange($from, $now);

        foreach ($symbols as $symbol) {
            $this->exchangeSnapshotRepository->add(
                new ExchangeSnapshot(
                    $symbol,
                    $this->currencyDataCollector->getAvgPrice($symbol)
                )
            );
        }

        $this->exchangeSnapshotRepository->save();
        $lock->release();
    }
}
