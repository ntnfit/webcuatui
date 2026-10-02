<?php

namespace App\Services\Gdt;

use App\Models\Company;
use App\Models\Customer;
use Illuminate\Support\Facades\RateLimiter;
use RuntimeException;

/**
 * Protects the portal (and our server) from a single customer or company hammering lookups and exports.
 * Limits are per company and per customer, so one account cannot exhaust capacity across many companies.
 */
class TaxRateLimiter
{
    private const LIMITS = [
        // action => [attempts per company, attempts per customer, window in seconds]
        'sync' => [20, 40, 600],
        'export' => [5, 10, 3600],
        'connect' => [10, 20, 600],
    ];

    /** @throws RuntimeException when the budget for $action is exhausted */
    public function hit(string $action, Company $company, ?Customer $customer): void
    {
        [$perCompany, $perCustomer, $window] = self::LIMITS[$action];

        $keys = ["tax:{$action}:company:{$company->getKey()}" => $perCompany];
        if ($customer) {
            $keys["tax:{$action}:customer:{$customer->getKey()}"] = $perCustomer;
        }

        foreach ($keys as $key => $max) {
            if (RateLimiter::tooManyAttempts($key, $max)) {
                $wait = RateLimiter::availableIn($key);
                throw new RuntimeException('Bạn thao tác quá nhanh, vui lòng thử lại sau '.ceil($wait / 60).' phút.');
            }
        }

        foreach (array_keys($keys) as $key) {
            RateLimiter::hit($key, $window);
        }
    }
}
