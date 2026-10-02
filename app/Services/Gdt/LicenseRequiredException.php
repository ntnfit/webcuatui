<?php

namespace App\Services\Gdt;

/** Raised when a Tax feature is used by a company without a valid license. */
class LicenseRequiredException extends GdtException
{
    public function __construct()
    {
        parent::__construct(LicenseNotice::blockedMessage(), 403);
    }
}
