<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Console;

use App\CurrencyExchange\Domain\Exception\CurrencyApiNotFoundException;
use App\CurrencyExchange\Infrastructure\Provider\CurrencyApiProvider;
use App\Shared\Domain\Service\UlidService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand('app:exchange:wallets:add-more')]
class SyncWalletsConsole extends Command
{
    const RECOMMENDED_WALLETS_COUNT = 20;

    public function __construct(
        private readonly CurrencyApiProvider $currencyApiProvider
    )
    {
        parent::__construct();
    }

    /** @throws CurrencyApiNotFoundException */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $allowedSyncAssets = [
            'BTC',
            'XMR'
        ];
        foreach ($allowedSyncAssets as $asset) {
            $api = $this->currencyApiProvider->getApiByAsset($asset);

            $walletsList = $api->getWallets();

            if (count($walletsList->wallets) < self::RECOMMENDED_WALLETS_COUNT) {
                $neededToAdd = self::RECOMMENDED_WALLETS_COUNT - count($walletsList->wallets);

                for ($i = 0; $i < $neededToAdd; $i++) {
                    $api->createWallet(UlidService::generate());
                }
            }

            $io->text('Done');
        }


        return Command::SUCCESS;
    }
}
