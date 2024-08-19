<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Repository;

use App\CurrencyExchange\Domain\Entity\ExchangeSnapshot;
use App\CurrencyExchange\Domain\Entity\ExchangeSymbol;
use App\CurrencyExchange\Domain\Exception\ExchangeSnapshotNotFoundException;
use App\CurrencyExchange\Domain\Repository\ExchangeSnapshotRepositoryInterface;
use App\CurrencyExchange\Infrastructure\DTO\CurrencyExchangeSnapshotDTO;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class ExchangeSnapshotRepository
    extends ServiceEntityRepository
    implements ExchangeSnapshotRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ExchangeSnapshot::class);
    }

    public function add(ExchangeSnapshot $exchangeSnapshot, bool $flush = false): void
    {
        $this->_em->persist($exchangeSnapshot);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    /** @throws Exception */
    public function getLastIntoDTOByCurrencyAssets(string $baseAsset, string $quoteAsset): CurrencyExchangeSnapshotDTO
    {
        $sql = ('SELECT
                es.base_asset as base_asset,
                es.quote_asset as quote_asset,
                exchange_snapshot.price as price,
                exchange_snapshot.created_at as created_at
            FROM exchange_snapshot
            INNER JOIN exchange_symbol es on es.symbol = exchange_snapshot.symbol_id
            WHERE (
                (es.base_asset = :currency_one AND es.quote_asset = :currency_two)
                          OR
                (es.base_asset = :currency_two AND es.quote_asset = :currency_one)
            )
            ORDER BY created_at DESC LIMIT 1'
        );

        $stmt = $this->_em->getConnection()->prepare($sql);

        $result = $stmt->executeQuery([
            'currency_one' => $baseAsset,
            'currency_two' => $quoteAsset,
        ]);

        /** @var array{base_asset: string, quote_asset: string, price: float, created_at: string}| bool $rawItems */
        $rawItems = $result->fetchAssociative();

        if (false === $rawItems) {
            throw new ExchangeSnapshotNotFoundException('Exchange snapshot not found');
        }

        if ($baseAsset === $rawItems['base_asset']) {
            return new CurrencyExchangeSnapshotDTO(
                new DateTimeImmutable($rawItems['created_at']),
                $rawItems['base_asset'],
                $rawItems['quote_asset'],
                (float)$rawItems['price']
            );
        } else {
            return new CurrencyExchangeSnapshotDTO(
                new DateTimeImmutable($rawItems['created_at']),
                $rawItems['quote_asset'],
                $rawItems['base_asset'],
                (1 / (float)$rawItems['price'])
            );
        }
    }

    /**
     * @throws NonUniqueResultException
     * @throws NoResultException
     */
    public function getLastByCurrencyAssets(ExchangeSymbol $symbol): ExchangeSnapshot
    {
        return $this->createQueryBuilder('s')
            ->where('s.symbol = :symbol')
            ->setParameter('symbol', $symbol)
            ->orderBy('s.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult();
    }
}
