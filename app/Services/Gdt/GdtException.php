<?php

namespace App\Services\Gdt;

use RuntimeException;

/** Any failure talking to the GDT portal. Messages never contain tokens or credentials. */
class GdtException extends RuntimeException
{
    public function __construct(string $message, public readonly int $status = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $status, $previous);
    }
}
