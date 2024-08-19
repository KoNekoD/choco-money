<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Entity;

use App\CurrencyExchange\Domain\CurrencyAPI\CurrencyApiEngineInterface;
use App\CurrencyExchange\Domain\CurrencyAPI\DTO\TransferResponseDTO;
use App\CurrencyExchange\Domain\Enum\DelayedTransferStatusEnum;
use App\CurrencyExchange\Domain\Event\LeadMoneyReceivedEvent;
use App\Shared\Domain\Entity\Aggregate;
use App\Shared\Domain\Service\UlidService;
use Carbon\Carbon;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
class DelayedTransfer extends Aggregate
{
    const MAX_WAIT_MINUTES = 30;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    #[ORM\Column]
    private readonly DateTimeImmutable $createdAt;

    #[ORM\Column]
    private readonly string $baseAsset;

    #[ORM\Column]
    private readonly string $quoteAsset;

    #[ORM\ManyToOne(targetEntity: ExchangeSnapshot::class)]
    #[ORM\JoinColumn(name: 'exchange_snapshot_id', referencedColumnName: 'id')]
    private ?ExchangeSnapshot $exchangeSnapshot;

    #[ORM\Column(nullable: true)]
    private ?string $mutualTransferTransactionKey;

    #[ORM\Column(enumType: DelayedTransferStatusEnum::class)]
    private DelayedTransferStatusEnum $status;

    #[ORM\OneToMany(
        mappedBy: 'delayedTransfer',
        targetEntity: DelayedTransferHistory::class,
        cascade: ['persist'],
        orphanRemoval: true
    )]
    private Collection $historyTracks;

    public function __construct(
        #[ORM\Column]
        private readonly string    $exchangerBaseWalletAddress,
        #[ORM\Column]
        private readonly float     $exchangerBaseWalletBalanceAmountBeforeReceiveMoney,
        #[ORM\Column]
        private readonly string    $exchangerBaseWalletName,
        #[ORM\Column]
        private readonly string    $leadBaseWalletAddress,
        #[ORM\Column]
        private readonly string    $leadQuoteWalletAddress,
        #[ORM\Column]
        private readonly float     $leadBaseExchangeAmount,
        #[ORM\Column]
        private readonly string    $leadEmail,
        CurrencyApiEngineInterface $baseEngine,
        CurrencyApiEngineInterface $quoteEngine
    )
    {
        $this->id = UlidService::generate();
        $this->createdAt = Carbon::now()->toDateTimeImmutable();
        $this->status = DelayedTransferStatusEnum::PENDING;
        $this->historyTracks = new ArrayCollection();
        $this->baseAsset = $baseEngine::getAsset();
        $this->quoteAsset = $quoteEngine::getAsset();
        $this->exchangeSnapshot = null;
        $this->mutualTransferTransactionKey = null;
    }

    public function convertToPending(): void
    {
        $this->convertTo(DelayedTransferStatusEnum::PENDING);
    }

    private function convertTo(DelayedTransferStatusEnum $newStatus, ?string $comment = null): void
    {
        $oldStatus = $this->status;
        if ($oldStatus !== $newStatus) {
            $this->status = $newStatus;
            $track = new DelayedTransferHistory(
                delayedTransfer: $this,
                comment: $comment ?? '',
                fromStatus: $oldStatus,
                toStatus: $newStatus
            );
            $this->historyTracks->add($track);
        }
    }

    public function convertToCancelled(?string $comment = null): void
    {
        $this->convertTo(DelayedTransferStatusEnum::PENDING, $comment);
    }

    public function convertToOverdue(): void
    {
        $this->convertTo(DelayedTransferStatusEnum::OVERDUE);
    }

    public function convertToMoneyReceived(ExchangeSnapshot $exchangeSnapshotInReceiveMoment): void
    {
        $this->convertTo(DelayedTransferStatusEnum::MONEY_RECEIVED);
        $this->exchangeSnapshot = $exchangeSnapshotInReceiveMoment;
        $this->raise(
            new LeadMoneyReceivedEvent($this->id)
        );
    }

    public function convertToMutualMoneySent(TransferResponseDTO $transferResponse): void
    {
        $this->mutualTransferTransactionKey = $transferResponse->transactionKey;
        $this->convertTo(DelayedTransferStatusEnum::MUTUAL_MONEY_SENT);
    }

    public function convertToExchanged(?string $comment = null): void
    {
        $this->convertTo(DelayedTransferStatusEnum::EXCHANGED, $comment);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getBaseAsset(): string
    {
        return $this->baseAsset;
    }

    public function getQuoteAsset(): string
    {
        return $this->quoteAsset;
    }

    public function getExchangeSnapshot(): ExchangeSnapshot
    {
        if (
            $this->status !== DelayedTransferStatusEnum::MONEY_RECEIVED &&
            $this->status !== DelayedTransferStatusEnum::MUTUAL_MONEY_SENT &&
            $this->status !== DelayedTransferStatusEnum::EXCHANGED
        ) {
            throw new DomainException(
                'You can access to exchange snapshot only with statuses: ' .
                'MONEY_RECEIVED, MUTUAL_MONEY_SENT, EXCHANGED'
            );
        }
        return $this->exchangeSnapshot;
    }

    public function getMutualTransferTransactionKey(): ?string
    {
        return $this->mutualTransferTransactionKey;
    }

    public function getStatus(): DelayedTransferStatusEnum
    {
        return $this->status;
    }

    public function getHistoryTracks(): Collection
    {
        return $this->historyTracks;
    }

    public function getExchangerBaseWalletAddress(): string
    {
        return $this->exchangerBaseWalletAddress;
    }

    public function getExchangerBaseWalletBalanceAmountBeforeReceiveMoney(): float
    {
        return $this->exchangerBaseWalletBalanceAmountBeforeReceiveMoney;
    }

    public function getExchangerBaseWalletName(): string
    {
        return $this->exchangerBaseWalletName;
    }

    public function getLeadBaseWalletAddress(): string
    {
        return $this->leadBaseWalletAddress;
    }

    public function getLeadQuoteWalletAddress(): string
    {
        return $this->leadQuoteWalletAddress;
    }

    public function getLeadBaseExchangeAmount(): float
    {
        return $this->leadBaseExchangeAmount;
    }

    public function getLeadEmail(): string
    {
        return $this->leadEmail;
    }
}
