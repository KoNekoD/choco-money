<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Console;

use App\CurrencyExchange\Domain\CurrencyDataCollector\CurrencyDataCollectorInterface;
use App\CurrencyExchange\Domain\Exception\CurrencyApiNotFoundException;
use App\CurrencyExchange\Domain\Repository\ExchangeSymbolRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Collect symbols if it first runs
 */
#[AsCommand('app:exchange:symbol:sync')]
class SyncCurrencySymbolsConsole extends Command
{
    public function __construct(
        private readonly CurrencyDataCollectorInterface    $currencyDataCollector,
        private readonly ExchangeSymbolRepositoryInterface $symbolRepository,
        private readonly EntityManagerInterface            $entityManager,
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $exchangeInfo = $this->currencyDataCollector->exchangeInfo();

        $totalFiltered = count($exchangeInfo->symbols);
        $io->text("Total symbols: $totalFiltered");

        foreach ($exchangeInfo->symbols as $symbol) {
            try {
                $this->symbolRepository->getExchangeSymbol($symbol);
            } catch (CurrencyApiNotFoundException) {
                continue;
            }
        }

        $this->entityManager->flush();

        $io->text('Done');

        return Command::SUCCESS;
    }
}
