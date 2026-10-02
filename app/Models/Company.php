<?php

namespace App\Models;

use App\Services\Gdt\LicenseRequiredException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Company extends Model
{
    use HasFactory;

    protected $fillable = ['owner_id', 'name', 'mst', 'address'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'owner_id');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'company_customer')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function gdtSession(): HasOne
    {
        return $this->hasOne(GdtSession::class);
    }

    public function taxInvoices(): HasMany
    {
        return $this->hasMany(TaxInvoice::class);
    }

    public function taxExportRuns(): HasMany
    {
        return $this->hasMany(TaxExportRun::class);
    }

    public function taxLicenses(): HasMany
    {
        return $this->hasMany(TaxLicense::class);
    }

    /**
     * The effective license: a currently valid one with the latest expiry or, when none is
     * valid, the active one that expired last (so notices can show the lapsed date).
     */
    public function activeLicense(): ?TaxLicense
    {
        return $this->taxLicenses()->valid()->orderByDesc('expires_at')->first()
            ?? $this->taxLicenses()->where('status', TaxLicense::STATUS_ACTIVE)->orderByDesc('expires_at')->first();
    }

    /** Active status and starts_at <= now <= expires_at on at least one license row. */
    public function hasValidLicense(): bool
    {
        return $this->taxLicenses()->valid()->exists();
    }

    /** Whole days until the effective license ends; 0 when there is no valid license. */
    public function licenseDaysLeft(): int
    {
        $license = $this->taxLicenses()->valid()->orderByDesc('expires_at')->first();

        return $license ? max(0, (int) ceil(now()->diffInDays($license->expires_at, false))) : 0;
    }

    /** @throws LicenseRequiredException */
    public function assertLicensed(): void
    {
        if (! $this->hasValidLicense()) {
            throw new LicenseRequiredException;
        }
    }

    /** Whether the customer belongs to this company (any role). */
    public function hasMember(Customer $customer): bool
    {
        return $this->members()->whereKey($customer->getKey())->exists();
    }

    public function isOwnedBy(Customer $customer): bool
    {
        return $this->members()
            ->whereKey($customer->getKey())
            ->wherePivot('role', 'owner')
            ->exists();
    }
}
