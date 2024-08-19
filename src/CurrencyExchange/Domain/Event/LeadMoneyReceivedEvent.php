<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Event;

use App\Shared\Domain\Event\EventInterface;

class LeadMoneyReceivedEvent implements EventInterface
{
    public function __construct(public readonly string $transferId)
    {
    }
}
