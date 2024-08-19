<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Repository;

use App\CurrencyExchange\Domain\DTO\ExchangeInfoSymbolDTO;
use App\CurrencyExchange\Domain\Entity\ExchangeSymbol;
use App\CurrencyExchange\Domain\Exception\ExchangeSymbolNotFoundException;
use App\CurrencyExchange\Domain\Repository\ExchangeSymbolRepositoryInterface;
use App\CurrencyExchange\Infrastructure\Provider\CurrencyApiProvider;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;

class ExchangeSymbolRepository extends ServiceEntityRepository implements ExchangeSymbolRepositoryInterface
{
    public function __construct(
        ManagerRegistry                      $registry,
        private readonly CurrencyApiProvider $currencyApiProvider
    )
    {
        parent::__construct($registry, ExchangeSymbol::class);
    }

    public function add(ExchangeSymbol $exchangeSymbol, bool $flush = false): void
    {
        $this->_em->persist($exchangeSymbol);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    /**
     * @inheritDoc
     */
    public function findOne(string $symbol): ExchangeSymbol
    {
        $result = $this->find($symbol);

        if (null === $result) {
            throw new ExchangeSymbolNotFoundException();
        }

        return $result;
    }

    /**
     * @throws Exception
     */
    public function getSymbolsForSnapshotByDateRange(DateTimeImmutable $from, DateTimeImmutable $to): array
    {
        $sql = ('SELECT s.symbol FROM exchange_symbol s
                 FULL OUTER JOIN exchange_snapshot es on s.symbol = es.symbol_id
                 WHERE
                     es.created_at is null OR
                     es.created_at = (
                        SELECT max(exchange_snapshot.created_at) FROM exchange_snapshot
                        WHERE exchange_snapshot.symbol_id = s.symbol) AND NOT
                     es.created_at BETWEEN :from_date and :to_date
                 GROUP BY s.symbol');

        $stmt = $this->_em->getConnection()->prepare($sql);

        $result = $stmt->executeQuery([
            'from_date' => $from->format('Y-m-d H:i:s'),
            'to_date' => $to->format('Y-m-d H:i:s'),
        ]);

        /** @var array<array{symbol: string}> $rawItems */
        $rawItems = $result->fetchAllAssociative();

        /** @var string[] $symbolsToCollect */
        $symbolsToCollect = array_column($rawItems, 'symbol');

        return $this->findBy(['symbol' => $symbolsToCollect]);
    }

    public function getExchangeSymbol(ExchangeInfoSymbolDTO $symbol): ExchangeSymbol
    {
        $exchangeSymbol = $this->find($symbol->symbol);

        if (null === $exchangeSymbol) {
            $exchangeSymbol = new ExchangeSymbol(
                $symbol->symbol,
                $this->currencyApiProvider->getApiByAsset($symbol->baseAsset),
                $this->currencyApiProvider->getApiByAsset($symbol->quoteAsset),
            );
            $this->_em->persist($exchangeSymbol);
        }
        return $exchangeSymbol;
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function findOneByAssets(string $currencyOne, string $currencyTwo): ExchangeSymbol
    {
        return $this->createQueryBuilder('s')
            ->where('s.baseAsset = :currency_one AND s.quoteAsset = :currency_two')
            ->orWhere('s.baseAsset = :currency_two AND s.quoteAsset = :currency_one')
            ->setParameters(['currency_one' => $currencyOne, 'currency_two' => $currencyTwo])
            ->getQuery()
            ->getSingleResult();
    }
}
