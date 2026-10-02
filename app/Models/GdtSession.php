<?php

namespace App\Models;

use App\Services\Gdt\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * GDT portal token for one company. The portal username and password are never persisted.
 */
#[ScopedBy(CompanyScope::class)]
class GdtSession extends Model
{
    protected $fillable = ['company_id', 'token', 'expires_at'];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return [
            'token' => 'encrypted',
            'expires_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function isActive(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isFuture();
    }
}
