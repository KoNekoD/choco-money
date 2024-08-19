<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Entity;

use App\CurrencyExchange\Domain\Enum\DelayedTransferStatusEnum;
use App\Shared\Domain\Service\UlidService;
use Carbon\Carbon;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class DelayedTransferHistory
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue(strategy: 'NONE')]
    private readonly string $id;

    #[ORM\Column]
    private readonly DateTimeImmutable $createdAt;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: DelayedTransfer::class, inversedBy: 'historyTracks')]
        private readonly DelayedTransfer           $delayedTransfer,

        #[ORM\Column(type: 'text')]
        private readonly string                    $comment,

        #[ORM\Column(enumType: DelayedTransferStatusEnum::class)]
        private readonly DelayedTransferStatusEnum $fromStatus,

        #[ORM\Column(enumType: DelayedTransferStatusEnum::class)]
        private readonly DelayedTransferStatusEnum $toStatus,
    )
    {
        $this->id = UlidService::generate();
        $this->createdAt = Carbon::now()->toDateTimeImmutable();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getDelayedTransfer(): DelayedTransfer
    {
        return $this->delayedTransfer;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getFromStatus(): DelayedTransferStatusEnum
    {
        return $this->fromStatus;
    }

    public function getToStatus(): DelayedTransferStatusEnum
    {
        return $this->toStatus;
    }
}
