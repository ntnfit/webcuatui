<?php

namespace App\Services\Gdt;

/** The portal rejected the token (401): the company must reconnect and solve a new captcha. */
class GdtUnauthorizedException extends GdtException
{
    public function __construct(string $message = 'Phiên cổng thuế đã hết hạn, vui lòng kết nối lại.')
    {
        parent::__construct($message, 401);
    }
}
