<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Console;

use App\CurrencyExchange\Domain\Entity\DelayedTransfer;
use App\CurrencyExchange\Domain\Repository\DelayedTransferRepositoryInterface;
use Carbon\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:exchange:transfers:force-finalize-old-transfers')]
class ForceFinalizeOldTransfersConsole extends Command
{
    public function __construct(private readonly DelayedTransferRepositoryInterface $delayedTransferRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        foreach ($this->delayedTransferRepository->findPendingTransfers() as $transfer) {
            $diff = $transfer
                ->getCreatedAt()
                ->diff(Carbon::now()->toDateTimeImmutable());

            $io->note($transfer->getId(). " Diff: $diff->i");

            if ($diff->i > DelayedTransfer::MAX_WAIT_MINUTES) {
                $io->note($transfer->getId(). " Diff: $diff->i");
                $transfer->convertToOverdue();
            }
        }

        foreach ($this->delayedTransferRepository->findMutualMoneySentTransfers() as $transfer) {
            $diff = $transfer
                ->getCreatedAt()
                ->diff(Carbon::now()->toDateTimeImmutable());
            $max_wait = DelayedTransfer::MAX_WAIT_MINUTES;

            if ($diff->i > $max_wait) {
                $transfer->convertToExchanged();
            }
        }
        $this->delayedTransferRepository->save();
        return Command::SUCCESS;
    }
}
