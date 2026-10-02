<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Paid access period for one company. Renewals are additional rows; revoking flips the status.
 * Only administrators write these rows.
 */
class TaxLicense extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';

    public const STATUS_REVOKED = 'revoked';

    protected $fillable = ['company_id', 'starts_at', 'expires_at', 'status', 'notes', 'created_by'];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Active licenses whose period contains the current moment. */
    public function scopeValid(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE)
            ->where('starts_at', '<=', now())
            ->where('expires_at', '>=', now());
    }

    public function isValid(): bool
    {
        return $this->status === self::STATUS_ACTIVE
            && $this->starts_at->lte(now())
            && $this->expires_at->gte(now());
    }
}
