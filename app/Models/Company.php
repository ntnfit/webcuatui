<?php

namespace App\Models;

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
