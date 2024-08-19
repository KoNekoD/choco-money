<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Console;

use App\CurrencyExchange\Application\Command\CollectSnapshots\CollectSnapshotsCommand;
use App\Shared\Application\Command\CommandBusInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Needed collecting new actual currency exchanges data
 */
#[AsCommand('app:exchange:snapshots:collect')]
class CollectExchangeSnapshotsConsole extends Command
{
    public function __construct(private readonly CommandBusInterface $commandBus)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->commandBus->execute(new CollectSnapshotsCommand());

        return Command::SUCCESS;
    }
}
