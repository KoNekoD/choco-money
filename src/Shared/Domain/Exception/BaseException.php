<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exception;

use Exception;

abstract class BaseException extends Exception //implements HttpExceptionInterface
{
    public function __construct(string $message = '', int $code = 0)
    {
        parent::__construct($message, $code);
    }
}
