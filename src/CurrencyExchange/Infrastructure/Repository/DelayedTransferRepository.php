<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Infrastructure\Repository;

use App\CurrencyExchange\Domain\Entity\DelayedTransfer;
use App\CurrencyExchange\Domain\Enum\DelayedTransferStatusEnum;
use App\CurrencyExchange\Domain\Repository\DelayedTransferRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class DelayedTransferRepository extends ServiceEntityRepository implements DelayedTransferRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DelayedTransfer::class);
    }

    public function add(DelayedTransfer $delayedTransfer, bool $flush = false): void
    {
        $this->_em->persist($delayedTransfer);

        if ($flush) {
            $this->_em->flush();
        }
    }

    public function save(): void
    {
        $this->_em->flush();
    }

    public function findOne(string $id): DelayedTransfer
    {
        return $this->find($id);
    }

    public function findPendingTransfers(): array
    {
        return $this->findBy(['status' => DelayedTransferStatusEnum::PENDING]);
    }

    public function findMutualMoneySentTransfers(): array
    {
        return $this->findBy(['status' => DelayedTransferStatusEnum::MUTUAL_MONEY_SENT]);
    }

    public function findPendingTransfersByExchangerBaseWalletNames(array $names): array
    {
        return $this->findBy(
            [
                'status' => DelayedTransferStatusEnum::MUTUAL_MONEY_SENT,
                'exchangerBaseWalletName' => $names
            ]
        );
    }
}
