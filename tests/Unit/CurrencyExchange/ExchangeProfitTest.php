<?php

declare(strict_types=1);

namespace App\Tests\Unit\CurrencyExchange;

use App\CurrencyExchange\Domain\Entity\ExchangeSnapshot;
use App\CurrencyExchange\Domain\Entity\ExchangeSymbol;
use App\CurrencyExchange\Domain\Exception\CurrencyExchangeException;
use App\CurrencyExchange\Domain\Repository\ExchangeSnapshotRepositoryInterface;
use App\CurrencyExchange\Infrastructure\Controller\GetLastExchangeSnapshotController;
use App\CurrencyExchange\Infrastructure\DTO\CurrencyExchangeSnapshotDTO;
use App\CurrencyExchange\Infrastructure\Service\ExchangeCalculationService;
use Carbon\Carbon;
use LogicException;
use PHPUnit\Framework\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

class ExchangeProfitTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @throws CurrencyExchangeException
     */
    public function testCalculationsCorrect(): void
    {
        $getExchangeController = new GetLastExchangeSnapshotController(
            new ExchangeCalculationService(
                new class implements ExchangeSnapshotRepositoryInterface {
                    public function add(ExchangeSnapshot $exchangeSnapshot, bool $flush = false): void
                    {
                    }

                    public function save(): void
                    {
                    }

                    public function getLastIntoDTOByCurrencyAssets(string $baseAsset, string $quoteAsset): CurrencyExchangeSnapshotDTO
                    {
                        $defaultPrice = 22.22;
                        if ($baseAsset === 'BASE') {
                            return new CurrencyExchangeSnapshotDTO(
                                (new Carbon())->toDateTimeImmutable(),
                                'BASE',
                                'QUOTE',
                                $defaultPrice
                            );
                        } else {
                            return new CurrencyExchangeSnapshotDTO(
                                (new Carbon())->toDateTimeImmutable(),
                                'BASE',
                                'QUOTE',
                                1 / $defaultPrice
                            );
                        }
                    }

                    public function getLastByCurrencyAssets(ExchangeSymbol $symbol): ExchangeSnapshot
                    {
                        throw new LogicException('Not implemented');
                    }
                }
            )
        );

        $balanceCurrency = 'BASE';
        $balance = 100;
        $prevBaseBalance = $balance;
        $baseBalances = [];
        while ($balance > 0) {
            if ($balanceCurrency === 'BASE') {
                $response = $getExchangeController($balanceCurrency, 'QUOTE');
                $decodedResponse = json_decode($response->getContent(), true);
                $newCurrency = 'QUOTE';
            } else {
                $response = $getExchangeController($balanceCurrency, 'BASE');
                $decodedResponse = json_decode($response->getContent(), true);
                $newCurrency = 'BASE';
            }

            $newBalance = $balance / $decodedResponse['price'];

            $balance = $newBalance;
            $balanceCurrency = $newCurrency;

            if ($balance < 30 && $balanceCurrency === 'BASE') {
                $balance = 0;
            }

            if ($balanceCurrency === 'BASE' && $newBalance !== 0 && $prevBaseBalance !== 0) {
                $baseBalances[] = $balance;
                $this->assertLessThan($prevBaseBalance, $balance);
                $prevBaseBalance = $balance;
            }


        }

        $this->assertMatchesJsonSnapshot($baseBalances);
    }
}
