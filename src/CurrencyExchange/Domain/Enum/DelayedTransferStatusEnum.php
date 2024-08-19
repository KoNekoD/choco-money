<?php

declare(strict_types=1);

namespace App\CurrencyExchange\Domain\Enum;

/**
 * ||-> PENDING -> CANCELLED
 * PENDING -> OVERDUE
 * PENDING -> MONEY_RECEIVED
 *
 * CANCELLED -> X
 *
 * OVERDUE -> X
 *
 * MONEY_RECEIVED -> MUTUAL_MONEY_SENT
 *
 * MUTUAL_MONEY_SENT -> EXCHANGED
 *
 * EXCHANGED |->|
 */
enum DelayedTransferStatusEnum: string
{
    // Lead requested transfer. use segregated wallet and wait for his money to come into our wallet
    case PENDING = 'pending';

    // Lead cancelled transfer
    case CANCELLED = 'cancelled';

    // Lead didn't have time to refill the purse in time
    case OVERDUE = 'overdue';

    // Lead's money successfully received. Send converted money for lead's wallet
    case MONEY_RECEIVED = 'money_received';

    // Converted money sent to lead. Waiting for review or automatically convert to exchanged
    case MUTUAL_MONEY_SENT = 'mutual_money_sent';

    // Transfer completed successfully
    case EXCHANGED = 'exchanged';
}
