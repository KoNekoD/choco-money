<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Console;

use App\CurrencyExchange\Infrastructure\Provider\CurrencyApiProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Find current reserves of all currencies
 */
#[AsCommand('app:exchange:reserves:list')]
class ListCurrentReservesConsole extends Command
{
    public function __construct(private readonly CurrencyApiProvider $apiProvider)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $rows = [];
        foreach ($this->apiProvider->getNamesList() as $apiName) {
            $api = $this->apiProvider->getApi($apiName);

            $apiAccounts = $api->getWallets();
            $rows[] = [
                $apiName,
                $apiAccounts->totalBalance,
                $apiAccounts->totalUnlockedBalance,
                count($apiAccounts->wallets)
            ];

            foreach ($apiAccounts->wallets as $account) {
                $io->info(
                    sprintf(
                        "API: %s. Account #%d - Name: %s \n \tAddress: %s\n\tBalance: %s, Unlocked balance: %s",
                        $apiName,
                        $account->index,
                        $account->name,
                        $account->address,
                        $account->balance,
                        $account->unlockedBalance
                    )
                );
            }
        }
        $io->table(
            ['API', 'Total balance', 'Total unlocked balance', 'Own accounts'],
            $rows
        );

        return Command::SUCCESS;
    }
}
